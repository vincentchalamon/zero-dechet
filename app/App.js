import React from 'react';
import {applyMiddleware, compose, createStore} from 'redux';
import {Provider} from 'react-redux';
import axiosMiddleware from 'redux-axios-middleware';
import thunk from 'redux-thunk';

import client from './src/http';
import reducer from './src/reducers';
import {Main} from './src/components';

const store = createStore(reducer, compose(applyMiddleware(axiosMiddleware(client), thunk)));

const App = () => {
  return (
    <Provider store={store}>
      <Main/>
    </Provider>
  );
};
export default App;
