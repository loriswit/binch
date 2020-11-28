import Vue from "vue"
import VueRouter from "vue-router"

import store from "@/store"
import i18n from "@/plugins/i18n"

import Home from "@/views/Home"
import Group from "@/views/Group"
import Goto from "@/views/Goto"
import Edit from "@/views/Edit"
import Rounds from "@/views/Rounds"
import Pay from "@/views/Pay"
import New from "@/views/New"
import Members from "@/views/Members"
import Member from "@/views/Member"

Vue.use(VueRouter);

const routes = [
    {
        path: "/",
        component: Home,
        beforeEnter: (to, from, next) => {
            if (store.state.recent.length)
                next({
                    path: "/group/" + store.state.recent[0].id,
                    replace: true
                });
            else
                next();
        }
    },

    {path: "/home", component: Home},
    {path: "/new", component: New, meta: {title: "edit.title.new"}},
    {path: "/join", component: Goto, meta: {title: "goto.title"}},

    {path: "/:id", component: Group},
    {path: "/group/:id", component: Group},
    {path: "/group/:id/edit", component: Edit, meta: {title: "edit.title.edit"}},
    {path: "/group/:id/rounds", component: Rounds, meta: {title: "rounds.title"}},
    {path: "/group/:id/pay/:payer", component: Pay},
    {path: "/group/:id/members", component: Members},
    {path: "/group/:id/members/:member", component: Member},

    {path: "*", redirect: "/"},
];

const router = new VueRouter({
    routes,
    mode: "history"
});

router.beforeEach((to, from, next) => {
    if (to.meta.title)
        store.commit("setTitle", i18n.t(to.meta.title));

    next();
});

export default router
