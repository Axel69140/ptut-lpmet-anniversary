import Axios from '../services/caller.services';

const getAnecdotes = () => {    
    return Axios.get(import.meta.env.VITE_URL_API + '/anecdotes');
}

const getAnecdoteById = (anecdoteId) => {    
    return Axios.get(import.meta.env.VITE_URL_API + '/anecdotes/' + anecdoteId);
}

const createAnecdote = (anecdote) => {    
    return Axios.post(import.meta.env.VITE_URL_API + '/anecdotes/create', anecdote);
}

const editAnecdote = (anecdoteId, anecdote) => {    
    return Axios.patch(import.meta.env.VITE_URL_API + '/anecdotes/' + anecdoteId, anecdote);
}

const deleteAnecdote = (anecdoteId) => {    
    return Axios.delete(import.meta.env.VITE_URL_API + '/anecdotes/' + anecdoteId);
}

const deleteAnecdotes = (ids) => {    
    return Axios.delete(import.meta.env.VITE_URL_API + '/anecdotes/many', {
        data: {
            id: ids
        }          
    });
}

const clearAnecdoteTable = () => {    
    return Axios.delete(import.meta.env.VITE_URL_API + '/anecdotes/clear');
}

const exportAnecdoteData = () => {    
    return Axios.post(import.meta.env.VITE_URL_API + '/settings/export-csv', {
        datasToExport: ["Anecdote"],
        exportParticipant: true
    });
}

export const anecdoteService = {
    getAnecdotes,
    getAnecdoteById,
    createAnecdote,
    editAnecdote,
    deleteAnecdotes,
    deleteAnecdote,
    clearAnecdoteTable,
    exportAnecdoteData
}