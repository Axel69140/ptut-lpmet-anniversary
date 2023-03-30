import Axios from '../services/caller.services'
import store from '../store'

let login = (credentials) => {
    return Axios.post('/auth/login', credentials)
}

let logout = () => {
    store.commit('logout');
    this.$router.push('/');
}

let getToken = () => {
    let user = localStorage.getItem('user');
    if (user) {            
        user = JSON.parse(user);
        return user.token;
    }
    return null;
}

let saveToken = (token) => {
    localStorage.setItem('token', token)
}

let getId = () => {
    let user = localStorage.getItem('user');
    if (user) {            
        user = JSON.parse(user);
        return user.id;
    }
    return null;
}

let getMail = () => {
    let user = localStorage.getItem('user');
    if (user) {            
        user = JSON.parse(user);
        return user.email;
    }
    return '';
}

let getFirstName = () => {
    let user = localStorage.getItem('user');
    if (user) {            
        user = JSON.parse(user);
        return user.firstName;
    }
    return null
}

let getLastName = () => {
    let user = localStorage.getItem('user');
    if (user) {            
        user = JSON.parse(user);
        return user.lastName;
    }
    return null
}

let isLogged = () => {
    let user = localStorage.getItem('user');
    if (user) {            
        user = JSON.parse(user);
        return user.token != '' ? true : false;
    }
    return false;
}

export const accountService = {
    login,
    logout,
    saveToken,
    getToken,
    isLogged,
    getLastName,
    getFirstName,
    getMail,
    getId
}