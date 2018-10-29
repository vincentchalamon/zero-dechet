import axios from 'axios';

class UserResource {
  get(iri) {
    return axios.get(iri);
  }

  login(email, password) {
    return axios.post('/login', {
      email: email,
      password: password,
    });
  }

  logout() {
    return axios.post('/logout');
  }
}

export default new UserResource();
