import React, {Fragment} from 'react';
import {Image, ScrollView, StyleSheet, View} from 'react-native';
import {Text} from 'react-native-paper';
import {compose} from 'recompose';
import {connect} from 'react-redux';
import {createBottomTabNavigator, createDrawerNavigator, createStackNavigator} from 'react-navigation';
import I18n from '../../translations';
import {Dashboard, Login} from '../';

const withRedux = connect(
  state => {
    return {
      isAuthenticated: !!state.user,
    };
  },
  null,
);

const styles = StyleSheet.create({
  sidebar: {
    backgroundColor: '#424242',
    paddingTop: 30,
    paddingLeft: 20,
    paddingRight: 20,
    paddingBottom: 20,
  },
  topbar: {
    backgroundColor: '#424242',
  },
  body: {
    backgroundColor: 'rgba(76, 175, 80, .8)',
  },
});

const SideMenu = () => {
  return (
    <View style={[styles.sidebar, {height: '100%'}]}>
      <Text style={{color: 'white', fontSize: 25, marginBottom: 30}}>
        <Image source={require('../../../assets/logo.png')} style={{width: 140, height: 140}}/>
        Zéro Déchet
      </Text>
      <ScrollView>
        <Text style={{color: 'white'}}>Aide</Text>
        <Text style={{color: 'white'}}>CGU</Text>
        <Text style={{color: 'white'}}>Mentions Légales</Text>
        <Text style={{color: 'white'}}>Le Zéro Déchet</Text>
      </ScrollView>
    </View>
  );
};

const DashboardNavigator = createBottomTabNavigator({
  Dashboard: () => <Dashboard/>,
  // Shops: () => <Shops/>,
  // Events: () => <Events/>,
  // Account: () => <Account/>,
});

const LoginNavigator = createStackNavigator({
  Login: () => <Login/>,
  // ForgotPassword: () => <ForgotPassword/>,
  // Register: () => <Register/>,
});

const Theme = compose(
  withRedux,
)(({isAuthenticated}) => {
  return (
    <Fragment>
      <Appbar.Header style={styles.topbar}>
        <Appbar.Action icon="menu"/>
        <Appbar.Content title={I18n.t('title')}/>
      </Appbar.Header>
      {isAuthenticated ? <DashboardNavigator/> : <LoginNavigator/>}
    </Fragment>
  );
});

const SideBarNavigator = createDrawerNavigator({
  Main: () => <Theme/>,
}, {
  contentComponent: SideMenu,
});

export default SideBarNavigator;
