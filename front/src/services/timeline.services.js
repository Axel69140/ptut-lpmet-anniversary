import Axios from '../services/caller.services';

const getTimelineSteps = () => {    
    return Axios.get(import.meta.env.VITE_URL_API + '/timelinesteps');
}

const getTimelineStepById = (timelineStepId) => {    
    return Axios.get(import.meta.env.VITE_URL_API + '/timelinesteps/' + timelineStepId);
}

const createTimelineStep = (timeline) => {    
    return Axios.post(import.meta.env.VITE_URL_API + '/timelinesteps/create', timeline);
}

const editTimelineStep = (timelineStepId, timeline) => {    
    return Axios.patch(import.meta.env.VITE_URL_API + '/timelinesteps/' + timelineStepId, timeline);
}

const deleteTimelineStep = (timelineStepId) => {    
    return Axios.delete(import.meta.env.VITE_URL_API + '/timelinesteps/' + timelineStepId);
}

const deleteTimelineSteps = (ids) => {    
    return Axios.delete(import.meta.env.VITE_URL_API + '/timelinesteps/many', {
        data: {
            id: ids
        }          
    });
}

const clearTimelineTable = () => {    
    return Axios.delete(import.meta.env.VITE_URL_API + '/timelinesteps/clear');
}

const exportTimelineData = () => {    
    return Axios.post(import.meta.env.VITE_URL_API + '/settings/export-csv', {
        datasToExport: ["TimelineStep"],
        exportParticipant: false
    });
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