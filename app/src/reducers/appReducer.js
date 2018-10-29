export const LOADING = 'LOADING';
export const NOTIFY = 'NOTIFY';

export default (state = {
  isStarting: true,
  isLoading: false,
  message: false,
}, action) => {
  state.isLoading = false;
  state.isStarting = false;

  switch (action.type) {
    case LOADING:
      return {
        ...state,
        isLoading: true,
      };
    case NOTIFY:
      return {
        ...state,
        message: action.message,
      };
    default:
      return {
        ...state,
      };
  }
};
