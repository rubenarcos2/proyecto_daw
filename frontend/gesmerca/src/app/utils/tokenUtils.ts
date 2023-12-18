export default class TokenUtils {
  /*
   * Return a date of token life time from token
   */
  static tokenExpirationTime = (): number => {
    let token = JSON.parse(sessionStorage.getItem('authUser') as string).access_token;
    token = JSON.parse(atob(token.split('.')[1]));
    return token.exp;
  };

  /*
   * Return a token life time in seconds from expires_in
   */
  static tokenLifeTime = (): number =>
    JSON.parse(sessionStorage.getItem('authUser') as string).expires_in; //In seconds
}
