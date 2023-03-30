import Axios from '../services/caller.services'
import store from '../store'

const logout = () => {
    store.commit('logout');
    this.$router.push('/');
}

const getToken = () => {
    let user = localStorage.getItem('user');
    if (user) {            
        user = JSON.parse(user);
        return user.token;
    }
    return null;
}

const getId = () => {
    let user = localStorage.getItem('user');
    if (user) {            
        user = JSON.parse(user);
        return user.id;
    }
    return null;
}

const getMail = () => {
    let user = localStorage.getItem('user');
    if (user) {            
        user = JSON.parse(user);
        return user.email;
    }
    return '';
}

const getFirstName = () => {
    let user = localStorage.getItem('user');
    if (user) {            
        user = JSON.parse(user);
        return user.firstName;
    }
    return null
}

const getLastName = () => {
    let user = localStorage.getItem('user');
    if (user) {            
        user = JSON.parse(user);
        return user.lastName;
    }
    return null
}

export const accountService = {
    logout,
    getToken,
    getLastName,
    getFirstName,
    getMail,
    getId
}