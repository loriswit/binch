const Storage = {
    putToken(groupID, token) {
        const data = localStorage.getItem("auth");
        const tokens = data ? JSON.parse(atob(data)) : {};

        tokens[groupID] = token;
        localStorage.setItem("auth", btoa(JSON.stringify(tokens)));
    },

    getToken(groupID) {
        const data = localStorage.getItem("auth");
        if (data) {
            const tokens = JSON.parse(atob(data));
            return tokens[groupID] || null;
        }
        return null;
    }
};

export default Storage;
