import Axios from '../services/caller.services';

Axios.post('/medias/create', formData).then(response => {
    console.log("test");
});