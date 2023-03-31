import Axios from '../services/caller.services';

const getArticles = () => {    
    return Axios.get('https://127.0.0.1:8000/articles');
}

const getArticleById = (articleId) => {    
    return Axios.get('https://127.0.0.1:8000/articles/' + articleId);
}

const createArticle = (article) => {    
    return Axios.post('https://127.0.0.1:8000/articles/create', article);
}

const editArticle = (articleId, article) => {    
    return Axios.patch('https://127.0.0.1:8000/articles/' + articleId, article);
}

const deleteArticle = (articleId) => {    
    return Axios.delete('https://127.0.0.1:8000/articles/' + articleId);
}

const deleteArticles = (ids) => {    
    return Axios.delete('https://127.0.0.1:8000/articles/many', {
        data: {
            id: ids
        }          
    });
}

const clearArticleTable = () => {    
    return Axios.delete('https://127.0.0.1:8000/articles/clear');
}

const exportArticleData = () => {    
    return Axios.get('https://127.0.0.1:8000/articles/export');
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