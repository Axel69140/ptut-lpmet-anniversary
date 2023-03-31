import Axios from '../services/caller.services';

const getParticipants = () => {    
    return Axios.get('https://127.0.0.1:8000/participants');
}

const getParticipantByEMail = (participantMail) => {    
    return Axios.get('https://127.0.0.1:8000/participants/' + participantMail);
}

const createParticipant = (participant) => {    
    return Axios.post('https://127.0.0.1:8000/participants/create', participant);
}

const editParticipant = (participantMail, participant) => {    
    return Axios.patch('https://127.0.0.1:8000/participants/' + participantMail, participant);
}

const deleteParticipant = (participantMail) => {    
    return Axios.delete('https://127.0.0.1:8000/participants/' + participantMail);
}

const deleteParticipants = (emails) => {    
    return Axios.delete('https://127.0.0.1:8000/participants/many', {
        data: {
            emails: emails
        }          
    });
}

const exportParticipantData = () => {    
    return Axios.get('https://127.0.0.1:8000/participants/export');
}

export const participantService = {
    getParticipants,
    getParticipantByEMail,
    createParticipant,
    editParticipant,
    deleteParticipants,
    deleteParticipant,
    exportParticipantData
}