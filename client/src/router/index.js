import Vue from "vue"
import VueRouter from "vue-router"
import Home from "@/views/Home"
import Group from "@/views/Group"
import Goto from "@/views/Goto"
import Edit from "@/views/Edit"
import Rounds from "@/views/Rounds"
import Pay from "@/views/Pay"
import New from "@/views/New";

Vue.use(VueRouter);

const routes = [
    {path: "/", component: Home},
    {path: "/new", component: New},
    {path: "/join", component: Goto},

    {path: "/:id", component: Group},
    {path: "/group/:id", component: Group},
    {path: "/group/:id/edit", component: Edit},
    {path: "/group/:id/rounds", component: Rounds},
    {path: "/group/:id/pay/:payer", component: Pay},

    {path: "*", redirect: "/"},
];

const router = new VueRouter({
    routes,
    mode: "history"
});

export default router
