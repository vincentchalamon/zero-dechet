import React from 'react';
import {connect} from 'react-redux';
import {Keyboard, TouchableOpacity, Text, TextInput as NativeTextInput, View} from 'react-native';
import {Button, TextInput} from 'react-native-paper';
import {compose} from 'recompose';
import {Formik, withFormik} from 'formik';
import * as yup from 'yup';
import {login} from '../../actions';
import I18n from '../../translations';

const withRedux = connect(
  state => {
    return {
      isLoading: state.app.isLoading,
    };
  },
  dispatch => {
    return {
      login: (email, password) => {
        dispatch(login(email, password));
      },
    };
  }
);

const Login = ({touched, errors, handleChange, handleSubmit, isLoading, navigation}) => {
  return (
    <Formik>
      <View>
        <TextInput onChangeText={handleChange('email')} label={I18n.t('email.label')} render={props => (
          <NativeTextInput keyboardType="email-address" {...props}/>
        )}/>
        {touched.email && errors.email && <Text>{errors.email}</Text>}

        <TextInput onChangeText={handleChange('password')} label={I18n.t('password.label')} render={props => (
          <NativeTextInput secureTextEntry={true} {...props}/>
        )}/>
        {touched.password && errors.password && <Text>{errors.password}</Text>}

        <TouchableOpacity onPress={() => navigation.navigate('ForgotPassword')}>
          <Text>{I18n.t('forgotPassword')}</Text>
        </TouchableOpacity>

        <Button mode="contained" title={I18n.t('login.label')} disabled={isLoading} onPress={handleSubmit}>
          {I18n.t('login.label')}
        </Button>

        {/*<SocialButton type="facebook">Se connecter avec Facebook</SocialButton>*/}
        {/*<SocialButton type="google">Se connecter avec Google</SocialButton>*/}
      </View>
    </Formik>
  );
};

export default compose(
  withRedux,
  withFormik({
    mapPropsToValues: () => ({
      email: '',
      password: '',
    }),
    // todo Translate error messages
    validationSchema: yup.object().shape({
      email: yup.string().email(I18n.t('email.invalid')).required(I18n.t('email.required')),
      password: yup.string().required(I18n.t('password.required')),
    }),
    handleSubmit: (values, {props, setSubmitting}) => {
      Keyboard.dismiss();
      props.login(values.email, values.password);
      setSubmitting(false);
    },
  }),
)(Login);
