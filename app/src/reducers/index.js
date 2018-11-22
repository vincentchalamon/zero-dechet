import { combineReducers } from 'redux';
import app, { LOADING, NOTIFY } from './appReducer';
import user, { LOAD_USER_ERROR, LOAD_USER_SUCCESS, LOGIN_ERROR, LOGIN_SUCCESS, LOGOUT } from './userReducer';

export {
  LOADING,
  NOTIFY,
  LOAD_USER_SUCCESS,
  LOAD_USER_ERROR,
  LOGIN_ERROR,
  LOGIN_SUCCESS,
  LOGOUT,
};
export default combineReducers({ app, user });
