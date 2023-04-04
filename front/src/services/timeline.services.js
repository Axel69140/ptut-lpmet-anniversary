import Axios from '../services/caller.services';

const getTimelineSteps = () => {    
    return Axios.get('https://127.0.0.1:8000/timelinesteps');
}

const getTimelineStepById = (timelineStepId) => {    
    return Axios.get('https://127.0.0.1:8000/timelinesteps/' + timelineStepId);
}

const createTimelineStep = (timeline) => {    
    return Axios.post('https://127.0.0.1:8000/timelinesteps/create', timeline);
}

const editTimelineStep = (timelineStepId, timeline) => {    
    return Axios.patch('https://127.0.0.1:8000/timelinesteps/' + timelineStepId, timeline);
}

const deleteTimelineStep = (timelineStepId) => {    
    return Axios.delete('https://127.0.0.1:8000/timelinesteps/' + timelineStepId);
}

const deleteTimelineSteps = (ids) => {    
    return Axios.delete('https://127.0.0.1:8000/timelinesteps/many', {
        data: {
            id: ids
        }          
    });
}

const clearTimelineTable = () => {    
    return Axios.delete('https://127.0.0.1:8000/timelinesteps/clear');
}

const exportTimelineData = () => {    
    return Axios.get('https://127.0.0.1:8000/timelinesteps/export');
}

export const timelineService = {
    getTimelineSteps,
    getTimelineStepById,
    createTimelineStep,
    editTimelineStep,
    deleteTimelineStep,
    deleteTimelineSteps,
    clearTimelineTable,
    exportTimelineData
}