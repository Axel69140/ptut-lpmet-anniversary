import Home from '../page/unlogged/Home.vue';
import Login from '../page/auth/Login.vue';
import Article from '../page/unlogged/Article.vue';
import Articles from '../page/unlogged/Articles.vue';
import Contact from '../page/unlogged/Contact.vue';
import Event from '../page/unlogged/Event.vue';
import Users from '../page/logged/Users.vue';
import User from '../page/logged/User.vue';
import EventForm from '../page/logged/EventForm.vue';
import ArticleForm from '../page/logged/ArticleForm.vue';
import AnecdoteForm from '../page/logged/AnecdoteForm.vue';
import ActivityForm from '../page/logged/ActivityForm.vue';
import ActivityManagement from '../page/admin/ActivityManagement.vue';
import AnecdoteManagement from '../page/admin/AnecdoteManagament.vue';
import ArticleManagement from '../page/admin/ArticleManagement.vue';
import ParticipantManagement from '../page/admin/ParticipantManagement.vue';
import TimelineManagement from '../page/admin/TimelineManagement.vue';
import UserManagement from '../page/admin/UserManagement.vue';
import { createRouter, createWebHistory } from 'vue-router';
import store from '../store';
import axios from 'axios';

// Check if user is admin
const isAdmin = (to, from, next) => {
    const user = store.state.user    
    if (user.token != '') {
      axios.get(`https://127.0.0.1:8000/users/${user.id}/role`, {
        headers: {
            Authorization: `Bearer ${user.token}`
        }
      }).then(response => {
        if (response.data.role[0] === 'ROLE_ADMIN') {
          next()
        } else {
          next('/')
        }                         
      }); 
    } else {
      next('/')
    }
}

// Check if user is connected
const isUserConnected = (to, from, next) => {
    if (store.state.user.userId != -1) {
      next()
    } else {
      next('/')
    }
}

const routes = [
    { path: '/', name: 'Home', component: Home },
    { path: '/login', name: 'Login', component: Login },
    { path: '/article', name: 'Article', component: Article },
    { path: '/articles', name: 'Articles', component: Articles },
    { path: '/contact', name: 'Contact', component: Contact },
    { path: '/event', name: 'Event', component: Event },
    { path: '/users', name: 'Users', component: Users, beforeEnter: isUserConnected },
    { path: '/user', name: 'User', component: User, beforeEnter: isUserConnected },
    { path: '/event/registration', name: 'EventForm', component: EventForm, beforeEnter: isUserConnected },
    { path: '/article/form', name: 'ArticleForm', component: ArticleForm, beforeEnter: isUserConnected },
    { path: '/anecdote/form', name: 'AnecdoteForm', component: AnecdoteForm, beforeEnter: isUserConnected },
    { path: '/activity/form', name: 'ActivityForm', component: ActivityForm, beforeEnter: isUserConnected },
    { path: '/admin/activity', name: 'ActivityManagement', component: ActivityManagement, beforeEnter: isAdmin },
    { path: '/admin/anecdote', name: 'AnecdoteManagement', component: AnecdoteManagement, beforeEnter: isAdmin },
    { path: '/admin/article', name: 'ArticleManagement', component: ArticleManagement, beforeEnter: isAdmin },
    { path: '/admin/participant', name: 'ParticipantManagement', component: ParticipantManagement, beforeEnter: isAdmin },
    { path: '/admin/timeline', name: 'TimelineManagement', component: TimelineManagement, beforeEnter: isAdmin },
    { path: '/admin/user', name: 'UserManagement', component: UserManagement, beforeEnter: isAdmin },    
    { path: '/:catchAll(.*)', redirect: '/' },   
];

export const router = createRouter({
    history: createWebHistory(),
    routes
});

router.replace({ path: '*', redirect: '/' });


export default async function (fastify, opts) {
    fastify.get('/', async function (request, reply) {
      return { root: true }
    });
}