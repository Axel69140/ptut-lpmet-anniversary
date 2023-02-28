import Home from '../page/Home.vue'
import Users from '../page/Users.vue'



import { createRouter, createWebHistory } from 'vue-router'
const routes = [
    { path: '/', name: 'Home', component: Home },
    { path: '/users', name: 'Users', component: Users },
]

export const router = createRouter({
    history: createWebHistory(),
    routes
})


export default async function (fastify, opts) {
    fastify.get('/', async function (request, reply) {
      return { root: true }
    })
    fastify.register(Task, {prefix: 'Task'});  
  }