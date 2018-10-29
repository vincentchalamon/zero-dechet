import {LOAD_USER_ERROR, LOAD_USER_SUCCESS, LOGIN_ERROR, LOGIN_SUCCESS, LOGOUT} from '../reducers';
import {loading} from './appActions';
import {UserResource} from '../resources';
import I18n from '../translations';

export const loadUser = () => async dispatch => {
  try {
    dispatch(loading());

    // todo Call UserResource.get(iri) <= where does iri (/users/{id}) come from?
    return dispatch({
      type: LOAD_USER_SUCCESS,
      user: null,
    });
  } catch (e) {
    return dispatch({
      type: LOAD_USER_ERROR,
      error: e,
    });
  }
};

export const login = (email, password) => async dispatch => {
  try {
    dispatch(loading(true));

    const user = await UserResource.login(email, password);

    return dispatch({
      type: LOGIN_SUCCESS,
      user: user,
    });
  } catch (e) {
    dispatch(error(I18n.t('login.error')));

    return dispatch({
      type: LOGIN_ERROR,
      error: e,
    });
  }
};

export const logout = () => async dispatch => {
  dispatch(loading(true));

  await UserResource.logout();

  return dispatch({
    type: LOGOUT,
  });
};
