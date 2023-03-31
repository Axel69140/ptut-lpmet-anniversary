import Axios from '../services/caller.services';

const getActivities = () => {    
    return Axios.get('https://127.0.0.1:8000/activities');
}

const getActivityById = (activityId) => {    
    return Axios.get('https://127.0.0.1:8000/activities/' + activityId);
}

const createActivity = (activity) => {    
    return Axios.post('https://127.0.0.1:8000/activities/create', activity);
}

const editActivity = (activityId, activity) => {    
    return Axios.patch('https://127.0.0.1:8000/activities/' + activityId, activity);
}

const deleteActivity = (activityId) => {    
    return Axios.delete('https://127.0.0.1:8000/activities/' + activityId);
}

const deleteActivities = (ids) => {    
    return Axios.delete('https://127.0.0.1:8000/activities/many', {
        data: {
            id: ids
        }          
    });
}

const clearActivityTable = () => {    
    return Axios.delete('https://127.0.0.1:8000/activities/clear');
}

const exportActivityData = () => {    
    return Axios.get('https://127.0.0.1:8000/activities/export');
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