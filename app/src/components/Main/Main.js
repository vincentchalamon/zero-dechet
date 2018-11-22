import React, { Fragment } from 'react';
import { ActivityIndicator, StatusBar, StyleSheet, View } from 'react-native';
import { connect } from 'react-redux';
import { compose, lifecycle } from 'recompose';
import { loadUser } from '../../actions';
import Theme from './Theme';

const withRedux = connect(
  state => {
    return {
      isStarting: state.app.isStarting,
    };
  },
  dispatch => {
    return {
      loadUser: () => {
        dispatch(loadUser());
      }
    }
  }
);

const withLifecycle = lifecycle({
  componentDidMount() {
    this.props.loadUser();
  },
});

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
  },
  horizontal: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    padding: 10,
  }
});

const Main = ({ isStarting }) => {
  // todo Add fadeOut after starting
  if (true === isStarting) {
    return (
      <View style={[styles.container, styles.horizontal]}>
        <ActivityIndicator size="large"/>
      </View>
    );
  }

  return (
    <Fragment>
      <StatusBar hidden={true}/>
      <Theme/>
    </Fragment>
  );
};

export default compose(
  withRedux,
  withLifecycle,
)(Main);
