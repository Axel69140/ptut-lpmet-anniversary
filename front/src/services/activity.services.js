import Axios from '../services/caller.services';

const getActivities = () => {    
    return Axios.get(import.meta.env.VITE_URL_API + '/activities');
}

const getActivityById = (activityId) => {    
    return Axios.get(import.meta.env.VITE_URL_API + '/activities/' + activityId);
}

const createActivity = (activity) => {    
    return Axios.post(import.meta.env.VITE_URL_API + '/activities/create', activity);
}

const editActivity = (activityId, activity) => {    
    return Axios.patch(import.meta.env.VITE_URL_API + '/activities/' + activityId, activity);
}

const deleteActivity = (activityId) => {    
    return Axios.delete(import.meta.env.VITE_URL_API + '/activities/' + activityId);
}

const deleteActivities = (ids) => {    
    return Axios.delete(import.meta.env.VITE_URL_API + '/activities/many', {
        data: {
            id: ids
        }          
    });
}

const clearActivityTable = () => {    
    return Axios.delete(import.meta.env.VITE_URL_API + '/activities/clear');
}

const exportActivityData = () => {    
    return Axios.post(import.meta.env.VITE_URL_API + '/settings/export-csv', {
        datasToExport: ["Activity"],
        exportParticipant: true
    });
}

export const activityService = {
    getActivities,
    getActivityById,
    createActivity,
    editActivity,
    deleteActivities,
    deleteActivity,
    clearActivityTable,
    exportActivityData
}