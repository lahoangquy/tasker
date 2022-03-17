import Vue from 'vue';
import Vuex from 'vuex';
import project from './project'
import category from './category'

Vue.use(Vuex);

export const store = new Vuex.Store({
    modules: {
        project,
        category
    },
    state: {},
    getters: {},
    mutations: {},
    actions: {}
});
