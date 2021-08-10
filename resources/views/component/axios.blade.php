<script type="text/javascript">
	const AX = axios.create({
	  timeout: 5000,
	  headers: {'Authorization': 'Bearer {{Auth::check()?Auth::User()->api_token??md5(date('dmyis')):md5(date('dmyis'))}}'},
	  validateStatus:function (status) {
	  	return status<=200;
	  }
	});

	var XHR_TOKEN=[];

	function ErrorXHR(error){
		var message='Error';
		switch(error.response.status){
			case 401:
			break;
		}
		return message;
	}


	const AXPOST = async function(url,data={}){
		try {
	       let res = await  axios.post(url,data,{
				 timeout: 5000,
				  headers: {'Authorization': 'Bearer {{Auth::check()?Auth::User()->api_token??md5(date('dmyis')):md5(date('dmyis'))}}'},
				  validateStatus:function (status) {
				  	return status<=200;
				  }
				
				});
		        
		        if(res.status == 200){
		            // test for status you want, etc
		            console.log(res.status)
		        }    
		        // Don't forget to return something   
		        return res.data
	    }
	    catch (err) {
	        console.error(err);
	    }

	


	}

	const AXGET = async function(url,data={}){
		try {
	       let res = await  axios.get(url,data,{
				 timeout: 5000,
				  headers: {'Authorization': 'Bearer {{Auth::check()?Auth::User()->api_token??md5(date('dmyis')):md5(date('dmyis'))}}'},
				  validateStatus:function (status) {
				  	return status<=200;
				  }
				
				});
		        
		        if(res.status == 200){
		            // test for status you want, etc
		            console.log(res.status)
		        }    
		        // Don't forget to return something   
		        return res.data
	    }
	    catch (err) {
	        console.error(err);
	    }

	


	}
</script>