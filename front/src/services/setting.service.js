import Axios from '../services/caller.services';

const getSettings = () => {    
    return Axios.get('https://127.0.0.1:8000/settings');
}

const resetSettings = () => {    
    return Axios.patch('https://127.0.0.1:8000/settings/reset');
}

const editSettings = (setting) => {    
    return Axios.patch('https://127.0.0.1:8000/settings/update', setting);
}

export const settingService = {
    getSettings,
    resetSettings,
    editSettings,
}