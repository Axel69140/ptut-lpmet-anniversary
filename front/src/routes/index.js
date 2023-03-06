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
const routes = [
    { path: '/', name: 'Home', component: Home },
    { path: '/login', name: 'Login', component: Login },
    { path: '/article', name: 'Article', component: Article },
    { path: '/articles', name: 'Articles', component: Articles },
    { path: '/contact', name: 'Contact', component: Contact },
    { path: '/event', name: 'Event', component: Event },
    { path: '/users', name: 'Users', component: Users },
    { path: '/user', name: 'User', component: User },
    { path: '/event/registration', name: 'EventForm', component: EventForm },
    { path: '/article/form', name: 'ArticleForm', component: ArticleForm },
    { path: '/anecdote/form', name: 'AnecdoteForm', component: AnecdoteForm },
    { path: '/activity/form', name: 'ActivityForm', component: ActivityForm },
    { path: '/admin/activity', name: 'ActivityManagement', component: ActivityManagement },
    { path: '/admin/anecdote', name: 'AnecdoteManagement', component: AnecdoteManagement },
    { path: '/admin/article', name: 'ArticleManagement', component: ArticleManagement },
    { path: '/admin/participant', name: 'ParticipantManagement', component: ParticipantManagement },
    { path: '/admin/timeline', name: 'TimelineManagement', component: TimelineManagement },
    { path: '/admin/user', name: 'UserManagement', component: UserManagement },    
];

export const router = createRouter({
    history: createWebHistory(),
    routes
});


export default async function (fastify, opts) {
    fastify.get('/', async function (request, reply) {
      return { root: true }
    });
}