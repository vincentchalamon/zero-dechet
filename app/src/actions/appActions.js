import {LOADING} from '../reducers';

export const loading = () => dispatch => {
  return dispatch({
    type: LOADING,
  });
};
