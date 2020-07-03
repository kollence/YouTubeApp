import React from 'react';
import { Grid } from '@material-ui/core';
import { BrowserRouter as Router, Route, Switch } from 'react-router-dom'
import { Admin } from "./admin/Admin";
import { Content } from "./pages/Content"
import { Nav } from "./components/Nav/Nav"
import { Login } from './pages/Login';
import { SingleItem } from './pages/SingleItem';
import AppProvider from './context/AppProvider';
import { PodcastsPage } from './pages/PodcastsPage';
import { Foot } from './components/Foot';
  

function App() {

  const styled = {
    backgroundColor: "#282c34",
    height: "100%"
  }
  return (
      <AppProvider>
           <Router>
            <Grid container direction="column" style={styled}>
              <Grid item>
                <Nav />
              </Grid>
              <Grid item container>
                <Grid item xs={false} sm={2} />
                  <Grid item xs={12} sm={8}> 
                  <Switch>
                  <Route exact path="/" component={Content}></Route>
                  <Route exact path="/page/:id" component={Content}></Route>
                  <Route exact path="/admin/:username" component={Admin}></Route>
                  <Route exact path="/login" component={Login}></Route>
                  <Route exact path="/single/:index" component={SingleItem}></Route>
                  </Switch>
                  </Grid>
                <Grid item xs={false} sm={2} />
              </Grid>
              <Grid item xs={12} style={{backgroundColor: "#bb8500", marginTop:"20px"}}>
                <Foot />
            </Grid>
            </Grid> 
          </Router>
      </AppProvider>
  );
}

export default App;
