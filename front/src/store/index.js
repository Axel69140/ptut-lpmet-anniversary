import { createStore } from 'vuex';
import axios from 'axios';

const instance = axios.create({
  baseURL: 'https://127.0.0.1:8000/users'
});

// Set user's settings (id, token)
let user = localStorage.getItem('user');
if (!user) {
 user = {
    token: ''
  }; 
} else {
  try {
    user = JSON.parse(user);
  } catch (ex) {
    user = {
      token: ''
    };
  }
}

// Create a new store instance.
const store = createStore({
  state: {
    status: '',
    user: user,
  },
  mutations: {
    setStatus: function (state, status) {
      state.status = status;
    },
    logUser: function (state, user) {
      localStorage.setItem('user', JSON.stringify(user));
      state.user = user;
    },
    logout: function (state) {
      state.user = {
        token: '',
      }
      localStorage.removeItem('user');
    }
  },
  actions: {
    login: ({commit}, userInfos) => {
      commit('setStatus', 'loading');
      return new Promise((resolve, reject) => {
        instance.post('/login', userInfos)
        .then(function (response) {
          commit('setStatus', '');
          commit('logUser', response.data);
          resolve(response);
        })
        .catch(function (error) {
          commit('setStatus', 'error_login');
          reject(error);
        });
      });
    },
    createAccount: ({commit}, userInfos) => {
      commit('setStatus', 'loading');
      return new Promise((resolve, reject) => {
        commit;
        instance.post('/register', userInfos)
        .then(function (response) {
          commit('setStatus', 'created');
          resolve(response);
        })
        .catch(function (error) {
          commit('setStatus', 'error_create');
          reject(error);
        });
      });
    }
  }
})

export default store;