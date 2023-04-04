import axios from 'axios';
import store from '../store';
import { router } from '../routes';
import { accountService } from './account.services';

const Axios = axios.create({
    baseURL: 'https://127.0.0.1:8000'
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
    console.log(error.response.status);
    if (error.response.status === 401) {
        store.commit('logout');
        router.push('/login');
    }    
});

export default Axios