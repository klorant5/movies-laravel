/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
import MoviesListComponent from './components/MoviesListComponent.vue';
import MovieComponent from './components/MovieComponent.vue';
import ActorComponent from './components/ActorComponent.vue';
import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Vue.component('movies-list', require('./components/MoviesListComponent.vue').default);


const routes = [
    { path: '/', component: MoviesListComponent },
    { path: '/movie/:id', component: MovieComponent, name: 'movie_page' },
    { path: '/actor/:id', component: ActorComponent, name: 'actor_page' }
];

const router = new VueRouter({
    routes // short for `routes: routes`
})

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    router
}).$mount('#app');
