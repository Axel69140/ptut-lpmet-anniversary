import Axios from '../services/caller.services';

const getGuests = () => {    
    return Axios.get(import.meta.env.VITE_URL_API + '/guests');
}

const getGuestById = (guestId) => {    
    return Axios.get(import.meta.env.VITE_URL_API + '/guests/' + guestId);
}

const createGuest = (guest) => {    
    return Axios.post(import.meta.env.VITE_URL_API + '/guests/create', guest);
}

const editGuest = (guestId, guest) => {    
    return Axios.patch(import.meta.env.VITE_URL_API + '/guests/' + guestId, guest);
}

const deleteGuest = (guestId) => {    
    return Axios.delete(import.meta.env.VITE_URL_API + '/guests/' + guestId);
}

const deleteGuests = (ids) => {    
    return Axios.delete(import.meta.env.VITE_URL_API + '/guests/many', {
        data: {
            id: ids
        }          
    });
}

const clearGuestTable = () => {    
    return Axios.delete(import.meta.env.VITE_URL_API + '/guests/clear');
}

export const guestService = {
    getGuests,
    getGuestById,
    createGuest,
    editGuest,
    deleteGuest,
    deleteGuests,
    clearGuestTable,
}