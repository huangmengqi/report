import React, { Component } from 'react';
import axios from 'axios';
import './FullPost.css';

//Full Post.
class FullPost extends Component {
    state = {
        loadedPost:null
    }

    PostDeleteHandler = () => {
        axios.delete("/posts/" + this.props.id)
            .then(response => {
                console.log(response);
        })
    }


    //Post request
    componentDidUpdate() {
        if (this.props.id) {
            if (!this.state.loadedPost || (this.state.loadedPost && this.state.loadedPost.id!==this.props.id)) {
                axios.get("/posts/" + this.props.id)
                .then(response => {
                    // console.log(response);
                    this.setState({loadedPost:response.data})
            })
            }


        }
    }
    
    //display post
    render () {
        let post = <p style={{ textAlign: 'center' }}>Please select a Post!</p>;
        if (this.props.id) {
            post=<p style={{textAlisgn:'center'}}>Loading...</p>
        }
        if (this.state.loadedPost) {
            post = (
                <div className="FullPost">
                    <h1>{this.state.loadedPost.title}</h1>
                    <p>{this.state.loadedPost.body}</p>
                    <div className="Edit">
                        <button onClick={this.PostDeleteHandler} className="Delete">Delete</button>
                    </div>
                </div>
            );
        }
        return post;
    }
}

export default FullPost;