export const GET_REPOS = 'zero-dechet/repos/LOAD';
export const GET_REPOS_SUCCESS = 'zero-dechet/repos/LOAD_SUCCESS';
export const GET_REPOS_FAIL = 'zero-dechet/repos/LOAD_FAIL';

const reducer = (state = {
  repos: []
}, action) => {
  switch (action.type) {
    case GET_REPOS:
      return {
        ...state,
        loading: true
      };

    case GET_REPOS_SUCCESS:
      return {
        ...state,
        loading: false,
        repos: action.payload.data
      };

    case GET_REPOS_FAIL:
      return {
        ...state,
        loading: false,
        error: 'Error while fetching repositories'
      };

    default:
      return state;
  }
};
export default reducer;

export const listRepos = (user) => {
  return {
    type: GET_REPOS,
    payload: {
      request: {
        url: `/users/${user}/repos`
      }
    }
  };
};
