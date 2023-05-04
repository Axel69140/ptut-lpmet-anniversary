import Axios from '../services/caller.services';

const getArticles = () => {    
    return Axios.get(import.meta.env.VITE_URL_API + '/articles');
}

const getArticleById = (articleId) => {    
    return Axios.get(import.meta.env.VITE_URL_API + '/articles/' + articleId);
}

const createArticle = (article) => {    
    return Axios.post(import.meta.env.VITE_URL_API + '/articles/create', article);
}

const editArticle = (articleId, article) => {    
    return Axios.patch(import.meta.env.VITE_URL_API + '/articles/' + articleId, article);
}

const deleteArticle = (articleId) => {    
    return Axios.delete(import.meta.env.VITE_URL_API + '/articles/' + articleId);
}

const deleteArticles = (ids) => {    
    return Axios.delete(import.meta.env.VITE_URL_API + '/articles/many', {
        data: {
            id: ids
        }          
    });
}

const clearArticleTable = () => {    
    return Axios.delete(import.meta.env.VITE_URL_API + '/articles/clear');
}

const exportArticleData = () => {    
    return Axios.post(import.meta.env.VITE_URL_API + '/settings/export-csv', {
        datasToExport: ["Article"],
        exportParticipant: true
    });
}

export const articleService = {
    getArticles,
    getArticleById,
    createArticle,
    editArticle,
    deleteArticles,
    deleteArticle,
    clearArticleTable,
    exportArticleData
}