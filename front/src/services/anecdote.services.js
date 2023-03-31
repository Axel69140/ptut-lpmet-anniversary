import Axios from '../services/caller.services';

const getAnecdotes = () => {    
    return Axios.get('https://127.0.0.1:8000/anecdotes');
}

const getAnecdoteById = (anecdoteId) => {    
    return Axios.get('https://127.0.0.1:8000/anecdotes/' + anecdoteId);
}

const createAnecdote = (anecdote) => {    
    return Axios.post('https://127.0.0.1:8000/anecdotes/create', anecdote);
}

const editAnecdote = (anecdoteId, anecdote) => {    
    return Axios.patch('https://127.0.0.1:8000/anecdotes/' + anecdoteId, anecdote);
}

const deleteAnecdote = (anecdoteId) => {    
    return Axios.delete('https://127.0.0.1:8000/anecdotes/' + anecdoteId);
}

const deleteAnecdotes = (ids) => {    
    return Axios.delete('https://127.0.0.1:8000/anecdotes/many', {
        data: {
            id: ids
        }          
    });
}

const clearAnecdoteTable = () => {    
    return Axios.delete('https://127.0.0.1:8000/anecdotes/clear');
}

const exportAnecdoteData = () => {    
    return Axios.get('https://127.0.0.1:8000/anecdotes/export');
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