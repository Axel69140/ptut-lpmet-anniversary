import Axios from '../services/caller.services';

const getGuests = () => {    
    return Axios.get('https://127.0.0.1:8000/guests');
}

const getGuestById = (guestId) => {    
    return Axios.get('https://127.0.0.1:8000/guests/' + guestId);
}

const createGuest = (guest) => {    
    return Axios.post('https://127.0.0.1:8000/guests/register', guest);
}

const editGuest = (guestId, guest) => {    
    return Axios.patch('https://127.0.0.1:8000/guests/' + guestId, guest);
}

const deleteGuest = (guestId) => {    
    return Axios.delete('https://127.0.0.1:8000/guests/' + guestId);
}

const deleteGuests = (ids) => {    
    return Axios.delete('https://127.0.0.1:8000/guests/many', {
        data: {
            id: ids
        }          
    });
}

const clearGuestTable = () => {    
    return Axios.delete('https://127.0.0.1:8000/guests/clear');
}

const exportGuestData = () => {    
    return Axios.get('https://127.0.0.1:8000/guests/export');
}

export const guestService = {
    getGuests,
    getGuestById,
    createGuest,
    editGuest,
    deleteGuest,
    deleteGuests,
    clearGuestTable,
    exportGuestData
}