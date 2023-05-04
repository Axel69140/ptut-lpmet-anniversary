import axios from 'axios';
import store from '../store';
import { router } from '../routes';
import { accountService } from './account.services';

const Axios = axios.create({
    baseURL: import.meta.env.VITE_URL_API
})

Axios.interceptors.request.use(request => {    
    // Si connecté on ajoute le token dans l'entête
    if(accountService.getToken()){
        request.headers.Authorization = 'Bearer '+ accountService.getToken()
    }

    return request
})

/**
 * Interceptor des réponses de l'API
 */
Axios.interceptors.response.use(response => {
    return response
}, error => {   
    if (error.response.status === 401) {
        store.commit('logout');
        router.push('/login');
    }
});

export default Axios