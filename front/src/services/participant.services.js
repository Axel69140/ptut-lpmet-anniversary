import Axios from '../services/caller.services';

const getParticipants = () => {    
    return Axios.get(import.meta.env.VITE_URL_API + '/participants');
}

const getParticipantByEMail = (participantMail) => {    
    return Axios.get(import.meta.env.VITE_URL_API + '/participants/' + participantMail);
}

const createParticipant = (participant) => {    
    return Axios.post(import.meta.env.VITE_URL_API + '/participants/create', participant);
}


const deleteParticipants = (emails) => {    
    return Axios.delete(import.meta.env.VITE_URL_API + '/participants/many', {
        data: {
            email: emails
        }          
    });
}

const exportParticipantData = () => {    
    return Axios.post(import.meta.env.VITE_URL_API + '/settings/export-csv', {
        datasToExport: ["Guest", "User"],
        exportParticipant: true
    });
}

export const participantService = {
    getParticipants,
    getParticipantByEMail,
    createParticipant,
    deleteParticipants,
    exportParticipantData
}