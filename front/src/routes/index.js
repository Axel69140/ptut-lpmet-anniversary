import Home from '../page/unlogged/Home.vue';
import Login from '../page/auth/Login.vue';
import Article from '../page/unlogged/Article.vue';
import Articles from '../page/unlogged/Articles.vue';
import Contact from '../page/unlogged/Contact.vue';
import MentionsLegales from '../page/unlogged/MentionsLegales.vue';
import Plan from '../page/unlogged/Plan.vue';
import Event from '../page/unlogged/Event.vue';
import Users from '../page/logged/Users.vue';
import User from '../page/logged/User.vue';
import EventForm from '../page/logged/EventForm.vue';
import GuestForm from '../page/logged/GuestForm.vue';
import ArticleForm from '../page/logged/ArticleForm.vue';
import AnecdoteForm from '../page/logged/AnecdoteForm.vue';
import ActivityForm from '../page/logged/ActivityForm.vue';
import ActivityManagement from '../page/admin/ActivityManagement.vue';
import AnecdoteManagement from '../page/admin/AnecdoteManagament.vue';
import ArticleManagement from '../page/admin/ArticleManagement.vue';
import ParticipantManagement from '../page/admin/ParticipantManagement.vue';
import TimelineManagement from '../page/admin/TimelineManagement.vue';
import UserManagement from '../page/admin/UserManagement.vue';
import AdminManagement from '../page/admin/AdminManagement.vue';
import { createRouter, createWebHistory } from 'vue-router';
import Axios from '../services/caller.services';
import { accountService } from '../services/account.services';

// Check if user is admin
const isAdmin = (to, from, next) => {
    if (accountService.getToken()) {
      Axios.get(`https://127.0.0.1:8000/users/${accountService.getId()}/role`).then((response) => {
        if (response.data && response.data.role[0] === 'ROLE_ADMIN') {
          next()
        } else {
          next('/')
        }                         
      });       
    } else {
      next({ path: '/login', query: { isConnected: false }})
    }
}

// Check if user is connected
const isUserConnected = (to, from, next) => {
    if (accountService.getToken()) {
      next()
    } else {
      next({ path: '/login', query: { isConnected: false }})
    }
}

const routes = [
    { path: '/', name: 'Accueil', component: Home },
    { path: '/login', name: 'Connexion', component: Login },
    { path: '/contact', name: 'Contact', component: Contact },
    { path: '/mentions-legales', name: 'Mentions légales', component: MentionsLegales },
    { path: '/plan', name: 'Plan d\'accès', component: Plan },
    { path: '/articles',
      children: [
        { path: '', name: 'Articles', component: Articles },
        { path: 'form', name: 'Formulaire d\'article', component: ArticleForm, beforeEnter: isUserConnected },
        { path: ':id', component: Article }        
      ]
    },   
    { path: '/event',
      children: [
        { path: '', name: 'Evènement', component: Event },
        { path: 'registration', name: 'Formulaire d\'inscription à l\'évènement', component: EventForm, beforeEnter: isUserConnected },
        { path: 'invitation', name: 'Formulaire d\'ajout d\'un invité', component: GuestForm, beforeEnter: isUserConnected },
        { path: 'users', name: 'Utilisateurs qui participe à l\'évènement', component: Users, beforeEnter: isUserConnected,
          children : [ 
            { path: ':id', component: User, beforeEnter: isUserConnected } 
          ] 
        },
        { path: 'activity/form', name: 'Formulaire d\'activité', component: ActivityForm, beforeEnter: isUserConnected }
      ]
    },       
    { path: '/anecdote/form', name: 'Formulaire d\'anecdote', component: AnecdoteForm, beforeEnter: isUserConnected },            
    { path: '/admin', beforeEnter: isAdmin,
      children: [
        { path: '', component: AdminManagement },
        { path: 'activity', component: ActivityManagement },
        { path: 'anecdote', component: AnecdoteManagement },
        { path: 'article', component: ArticleManagement },
        { path: 'participant', component: ParticipantManagement },
        { path: 'timeline', component: TimelineManagement },
        { path: 'user', component: UserManagement } 
      ]
    },     
    { path: '/:catchAll(.*)', name:"All", redirect: '/' },   
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