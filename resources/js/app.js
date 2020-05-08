window.Vue = require('vue');

require('./bootstrap');

import VueRouter from 'vue-router';
import routes from './routes';
import store from './vuex/store';
import Api from './plugins/api.js';

Vue.use(VueRouter);
Vue.use(Api);

const router = new VueRouter(routes);

const app = new Vue({
    el: '#todoApp',
    store,
    router,
});
