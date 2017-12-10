
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

require('acacha-relationships');

Vue.component(
  'passport-clients',
  require('./components/passport/Clients.vue')
);

Vue.component(
  'passport-authorized-clients',
  require('./components/passport/AuthorizedClients.vue')
);

Vue.component(
  'passport-personal-access-tokens',
  require('./components/passport/PersonalAccessTokens.vue')
);

Vue.component('adminlte-flash-message', require('./components/adminlte/message/AdminlteFlashMessageComponent.vue'));

import ToggleButton from 'vue-js-toggle-button'
Vue.use(ToggleButton)

import Vuex from 'vuex';
Vue.use(Vuex);

const debug = process.env.NODE_ENV !== 'production'
const store = new Vuex.Store({strict: debug});

const app = new Vue({
  el: '#app',
  store,
});
