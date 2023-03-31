import Axios from '../services/caller.services';

const getUsers = () => {    
    return Axios.get('https://127.0.0.1:8000/users');
}

const getUserById = (userId) => {    
    return Axios.get('https://127.0.0.1:8000/users/' + userId);
}

const getGuestsByUser = (userId) => {
    return Axios.get('https://127.0.0.1:8000/users/' + userId + '/guests');
}

const createUser = (user) => {    
    return Axios.post('https://127.0.0.1:8000/users/register', user);
}

const editUser = (userId, user) => {    
    return Axios.patch('https://127.0.0.1:8000/users/' + userId, user);
}

const deleteUser = (userId) => {    
    return Axios.delete('https://127.0.0.1:8000/users/' + userId);
}

const deleteUsers = (ids) => {    
    return Axios.delete('https://127.0.0.1:8000/users/many', {
        data: {
            id: ids
        }          
    });
}

const clearUserTable = () => {    
    return Axios.delete('https://127.0.0.1:8000/users/clear');
}

const exportUserData = () => {    
    return Axios.get('https://127.0.0.1:8000/users/export');
}

export const userService = {
    getUsers,
    getUserById,
    getGuestsByUser,
    createUser,
    editUser,
    deleteUser,
    deleteUsers,
    clearUserTable,
    exportUserData
}