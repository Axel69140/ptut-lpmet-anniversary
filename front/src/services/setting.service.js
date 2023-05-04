import Axios from '../services/caller.services';

const getSettings = () => {    
    return Axios.get(import.meta.env.VITE_URL_API + '/settings');
}

const resetSettings = () => {    
    return Axios.patch(import.meta.env.VITE_URL_API + '/settings/reset');
}

const editSettings = (setting) => {    
    return Axios.patch(import.meta.env.VITE_URL_API + '/settings/update', setting);
}

export const settingService = {
    getSettings,
    resetSettings,
    editSettings,
}