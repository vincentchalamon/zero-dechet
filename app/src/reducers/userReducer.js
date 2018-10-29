export const LOAD_USER_SUCCESS = 'LOAD_USER_SUCCESS';
export const LOAD_USER_ERROR = 'LOAD_USER_ERROR';
export const LOGIN_SUCCESS = 'LOGIN_SUCCESS';
export const LOGIN_ERROR = 'LOGIN_ERROR';
export const LOGOUT = 'LOGOUT';

export default (state = null, action) => {
  switch (action.type) {
    case LOAD_USER_SUCCESS:
    case LOGIN_SUCCESS:
      return action.user;
    case LOAD_USER_ERROR:
    case LOGIN_ERROR:
    case LOGOUT:
      return null;
    default:
      return state;
  }
};
