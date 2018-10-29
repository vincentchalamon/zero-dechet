import React from 'react';
import {connect} from 'react-redux';
import {Text} from 'react-native';
import {compose} from 'recompose';

const Dashboard = () =>
  <Text>Dashboard</Text>
;

const withRedux = connect(
  state => {
    return {
      user: state.user,
    };
  },
  null
);

export default compose(
  withRedux,
)(Dashboard);
