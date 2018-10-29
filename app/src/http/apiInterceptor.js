import axios from 'axios';
import {ConstraintViolationsListError, InvalidRequestError} from '../exceptions';

axios.interceptors.request.use((config) => {
  config.baseURL = 'http://localhost:81';
  // todo Is it possible to override those parameters in a call?
  config.headers.common['Accept'] = 'application/ld+json';
  config.headers.post['Content-Type'] = 'application/ld+json';
  config.headers.put['Content-Type'] = 'application/ld+json';

  return config;
});

axios.interceptors.response.use((response) => {
  return response.data;
}, (error) => {
  const response = error.response;
  if (400 !== response.status) {
    return Promise.reject(response.data);
  }

  if ('ConstraintViolationList' !== response.data['@type']) {
    return Promise.reject(new InvalidRequestError(response.data));
  }

  let violations = {};
  response.data.violations.forEach((violation) => {
    violations[violation.propertyPath] = violation.message;
  });

  return Promise.reject(new ConstraintViolationsListError(violations));
});
