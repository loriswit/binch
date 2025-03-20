import Vue from "vue"
import Vuex from "vuex"

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        group: null,
        rounds: [],
        recent: [],

        title: null,
        locale: null,
        error: null,

        tokens: {},
        auth: {
            required: false,
            next: [] // functions to run after authentication
        },

        // function to run on refresh
        refresh: null,
        runningRequests: 0
    },
    getters: {
        loading: state => state.runningRequests > 0
    },
    mutations: {
        initStore(state) {
            // load tokens
            const tokens = localStorage.getItem("auth");
            if (tokens)
                state.tokens = JSON.parse(atob(tokens));

            // load recent groups
            const recent = localStorage.getItem("recent");
            if (recent)
                state.recent = JSON.parse(recent);

            // load locale
            state.locale = localStorage.getItem("locale") || navigator.language;
        },
        clearError(state) {
            state.error = null;
        },
        closeAuthDialog(state) {
            state.auth.required = false;
            state.auth.next = [];
        },
        onRefresh(state, func) {
            state.refresh = func;
        },
        putToken(state, {id, token}) {
            state.tokens[id] = token;
            localStorage.setItem("auth", btoa(JSON.stringify(state.tokens)));
        },
        putRecent(state, {id, name}) {
            state.recent = state.recent.filter(e => e.id !== id);
            state.recent.unshift({id: id, name: name});
            localStorage.setItem("recent", JSON.stringify(state.recent));
        },
        setTitle(state, title) {
            state.title = title;
        }
    },
    actions: {
        async editGroup(context, {group, newGroup}) {
            const response = newGroup ?
                await http.post(context, "groups", group) :
                await http.patch(context, "group/" + group.path, group, group.path);

            if (response.token)
                context.commit("putToken", {
                    id: group.path,
                    token: response.token
                });
        },

        async fetchGroup(context, id) {
            try {
                context.state.group = await http.get(context, "group/" + id);
                context.commit("putRecent", {
                    id: id,
                    name: context.state.group.name
                });

            } catch (error) {
                if (error === 404)
                    context.state.error = {msg: "group", value: id};

                throw error;
            }
        },

        async fetchRounds(context, id) {
            context.state.rounds = await http.get(context, "group/" + id + "/rounds");
            context.state.rounds.reverse();
        },

        async postRound(context, {id, rounds}) {
            await http.post(context, "group/" + id + "/rounds", rounds, id);
        },

        async deleteRound(context, {id, date, deleted}) {
            await http.patch(context, "group/" + id + "/round/" + date, {deleted: deleted}, id);
        }
    }
})

// HTTP helpers functions

const http = {

    async get(context, url) {
        return await this.sendRequest(context, {method: "GET", url: url})
    },

    async post(context, url, body, auth) {
        return await this.sendRequest(context, {method: "POST", url: url, body: body}, auth)
    },

    async patch(context, url, body, auth) {
        return await this.sendRequest(context, {method: "PATCH", url: url, body: body}, auth)
    },

    async sendRequest(context, options, auth = null) {
        if (process.env.VUE_APP_READ_ONLY && options.method !== "GET") {
            context.state.error = {msg: "read-only"};
            throw new Error(`Can't perform ${options.method} request: app is in read-only mode`);
        }

        ++context.state.runningRequests;

        if (auth) {
            const token = context.state.tokens[auth];
            if (token)
                options.headers = {Authorization: "Bearer " + token};
        }

        try {
            const response = await Vue.http(options);
            context.commit("clearError");
            return response.body;

        } catch (response) {
            scrollTo(0, 0);

            if (response.status === 0)
                context.state.error = {msg: navigator.onLine ? "disconnected" : "offline"};

            else if (response.status === 401 || response.status === 403) {
                context.state.auth.required = true;
                context.state.auth.next.push(() => this.sendRequest(context, options, auth));

            } else
                context.state.error = {
                    msg: "default",
                    value: (response.body.message || response.body.error)
                };

            throw response.status;

        } finally {
            --context.state.runningRequests;
        }
    }
};
