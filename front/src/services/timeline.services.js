import Axios from '../services/caller.services';

const getTimelineSteps = () => {    
    return Axios.get('https://127.0.0.1:8000/timelines');
}

const getTimelineStepById = (timelineStepId) => {    
    return Axios.get('https://127.0.0.1:8000/timelines/' + timelineStepId);
}

const createTimelineStep = (timeline) => {    
    return Axios.post('https://127.0.0.1:8000/timelines/create', timeline);
}

const editTimelineStep = (timelineStepId, timeline) => {    
    return Axios.patch('https://127.0.0.1:8000/timelines/' + timelineId, timelineStepId);
}

const deleteTimelineStep = (timelineStepId) => {    
    return Axios.delete('https://127.0.0.1:8000/timelines/' + timelineStepId);
}

const deleteTimelineSteps = (ids) => {    
    return Axios.delete('https://127.0.0.1:8000/timelines/many', {
        data: {
            id: ids
        }          
    });
}

const clearTimelineTable = () => {    
    return Axios.delete('https://127.0.0.1:8000/timelines/clear');
}

const exportTimelineData = () => {    
    return Axios.get('https://127.0.0.1:8000/timelines/export');
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