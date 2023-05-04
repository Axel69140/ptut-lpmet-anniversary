import Axios from '../services/caller.services';

const getUsers = () => {    
    return Axios.get(import.meta.env.VITE_URL_API + '/users');
}

const getUserById = (userId) => {    
    return Axios.get(import.meta.env.VITE_URL_API + '/users/' + userId);
}

const getGuestsByUser = (userId) => {
    return Axios.get(import.meta.env.VITE_URL_API + '/users/' + userId + '/guests');
}

const createUser = (user) => {    
    return Axios.post(import.meta.env.VITE_URL_API + '/users/register', user);
}

const editUser = (userId, user) => {    
    return Axios.patch(import.meta.env.VITE_URL_API + '/users/' + userId, user);
}

const deleteUser = (userId) => {    
    return Axios.delete(import.meta.env.VITE_URL_API + '/users/' + userId);
}

const deleteUsers = (ids) => {    
    return Axios.delete(import.meta.env.VITE_URL_API + '/users/many', {
        data: {
            id: ids
        }          
    });
}

const clearUserTable = () => {    
    return Axios.delete(import.meta.env.VITE_URL_API + '/users/clear');
}

const exportUserData = () => {    
    return Axios.post(import.meta.env.VITE_URL_API + '/settings/export-csv', {
        datasToExport: ["User"],
        exportParticipant: false
    });
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