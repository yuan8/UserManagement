const fs = require('fs');
const mysql = require('mysql');
const process = require('process');
const unicodestring = require('unicodechar-string');

const { Client, MessageMedia } = require('whatsapp-web.js');

var con = mysql.createConnection({
	host: "localhost",
	user: "root",
	password: "uba2013?",
	database: "whatapp_api",
	port: 3306
});


con.connect(function(err) {
	if(err) throw err;
	console.log("Database Connected!");
});

const SESSION_FILE_PATH = __dirname+'/app.session.json';
const SETTING_PATH=__dirname+'/app.setting.json';
const STATUS_APP_PATH=__dirname+'/app.status.json';

var STATUS_APP={};
var app_={};

var rawdata = fs.readFile(STATUS_APP_PATH,{encoding:'utf8', flag:'r'},(err,data)=>{
	if(!err){
		STATUS_APP= JSON.parse(data??'{}');

	}
});



if(fs.existsSync(SETTING_PATH)) {
    app_ = require(SETTING_PATH);
}else{
	app_={}
}



let sessionCfg;



function UpdateStatus(){
		var date=new Date();
		STATUS_APP.updated_at=date;
		fs.writeFile(STATUS_APP_PATH, JSON.stringify(STATUS_APP), function (err,success) {
        if(err) {
            console.error(err);
        }else{
        	var sqlauth = "SELECT * FROM apps where id=?";
			con.query(sqlauth,[app_.app_id], function (err13, result13, fields13) {
				if(typeof result13!=='undefined'){
					if(result13.length>0){
						var sql_token="UPDATE  apps  SET wa_pid=?, wa_status=?, wa_number=? , wa_state=? , updated_at=?  where id=?";
						con.query(sql_token, [STATUS_APP.wa_pid,STATUS_APP.wa_status,STATUS_APP.wa_number,STATUS_APP.wa_state,date,app_.app_id],function(err,res,field){
							if(err){
								console.log(err);
							}else{

							}
						});
					}
				}
				
			});

        }
    });
}





function getENV(){
	if(fs.existsSync(SESSION_FILE_PATH)) {
	    sessionCfg = require(SESSION_FILE_PATH);
	}else{
		sessionCfg=undefined;
	}
	return new Client({
		qrTimeoutMs:0,
		restartOnAuthFail: true,
		puppeteer: {
			headless: true,
			//executablePath: 'C:/Program Files (x86)/Google/Chrome/Application/chrome.exe', //Windows x86
			//executablePath: 'C:/Program Files/Google/Chrome/Application/chrome.exe', //Windows x64
			//executablePath: '/usr/bin/google-chrome-stable', //Linux
			//executablePath: '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome', //Mac OS
			executablePath: '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
			args: [
				'--no-sandbox',
				'--disable-setuid-sandbox',
				'--disable-dev-shm-usage',
				'--disable-accelerated-2d-canvas',
				'--no-first-run',
				'--no-zygote',
				'--single-process',
				'--disable-gpu'
			],
		},
		session: sessionCfg
	});
}

var client;

function initialize(){
    STATUS_APP.wa_token=null;
    STATUS_APP.wa_status=1;
    STATUS_APP.wa_state='initial';
	STATUS_APP.wa_pid=process.pid;
    UpdateStatus();

    if(fs.existsSync(SESSION_FILE_PATH)) {
	    sessionCfg = require(SESSION_FILE_PATH);
	}else{
		sessionCfg=undefined;
	}
	client=getENV();


	client.initialize().catch((err)=>{
		STATUS_APP.wa_status=0;
		STATUS_APP.wa_token=null;
    	UpdateStatus();
	});
}




// inital
initialize();

client.on('qr', (qr) => {
    STATUS_APP.wa_status=2;
    STATUS_APP.wa_token=qr;
	UpdateStatus();
	try {
		if(sessionCfg!==undefined){
	 		 fs.unlink(SESSION_FILE_PATH);
		}
	} catch(err) {
	  console.error(err);
	}

	
});

client.on('authenticated', (session) => {
    console.log('Terotentikasi-  ');
     STATUS_APP.wa_status=3;
     STATUS_APP.wa_token=null;
     STATUS_APP.wa_number=null;
	 UpdateStatus();
   	 sessionCfg=session;
    fs.writeFile(SESSION_FILE_PATH, JSON.stringify(session), function (err,success) {
        if(err) {
            console.error(err);
        }else{
        	
        }


    });
});

client.on('auth_failure', msg => {
	setTimeout(function(){
		initialize();

	},2000);
});

client.on('disconnected', (reason) => {
    console.log(reason);
    try {
    	switch(reason){
    		case "CONFLICT":{
    			setTimeout(function(){
					initialize();
				},300000);

    		}
    	}
		if(sessionCfg!==undefined){
	  		
		}

		STATUS_APP.wa_status=1;
  		STATUS_APP.wa_token=null;
  		STATUS_APP.wa_number=null;

  		UpdateStatus();
	} catch(err) {
	  console.error(err);
	}
	
  
    console.log('Mohon tunggu, sedang proses cetak QR Code BARU...');
});


client.on('change_state',(state)=>{
	 STATUS_APP.state=state;
	 console.log('\x1b[36m%s\x1b[0m',' stante change '+state);
	UpdateStatus();

});

function convertEMOJI(emoji) {
	var backStr = ""
	if(emoji&&emoji.length>0) {
		for(var char of emoji) {
			var index =  char.codePointAt(0);
			if(index>65535) {
				var h = '\\u'+(Math.floor((index - 0x10000) / 0x400) + 0xD800).toString(16);
				var c = '\\u'+((index - 0x10000) % 0x400 + 0xDC00).toString(16);
				backStr = backStr + h + c;
			} else {
				backStr = backStr + char;
			}
		}
		//console.log(backStr);
	}
	return backStr;
}

function phoneNumberFormatter(number) {
	//let formatted = number.replace(/\D/g, '');
	let formatted = number;
	if(formatted.startsWith('0')) {
		formatted = '62' + formatted.substr(1);
	}
	if(!formatted.endsWith('@c.us') && !formatted.endsWith('@g.us')) {
		formatted += '@c.us';
	} else if(!formatted.endsWith('@g.us')) {
		formatted += '@g.us';
	}
	return formatted;
}

const checkRegisteredNumber = async function(number) {
	const isRegistered = await client.isRegisteredUser(number);
	return isRegistered;
}

function getRndInteger(min, max) { //0,9
	//return Math.floor(Math.random() * ((max+1) - min)) + min; //0-9
	return Math.floor(Math.random() * (max - min)) + min; //0-8
}

var count_message=-1;
var interval_time_message_listed=1000;
var interval_time_message=1000;

 var sending_message=async function (res,i){
	var data=res[i]??null;

	if(data){
		var nomor_tujuan_kirim = phoneNumberFormatter(data.to_number);
		var ME=false;
		switch(data.message_type){
			case 1:
				ME=await client.sendMessage(nomor_tujuan_kirim, unicodestring(data.content_text+' '+Date()));
			break;
			case 2:
				ME=[];
				var FILES=JSON.parse(data.content_attach??'[]');
				var first=false;
				for(var f in FILES){
						if(fs.existsSync(__dirname+FILES[f])){
							const media = await MessageMedia.fromFilePath(__dirname+FILES[f]);

							if(!first){
								ME.push(await client.sendMessage(nomor_tujuan_kirim, media, { caption: unicodestring(data.content_text) }));
								first=true;
							}else{
								ME.push(await client.sendMessage(nomor_tujuan_kirim, media));
							}
						}
					
					

				}
			break;
		}

		if(ME){
			if(Array.isArray(ME) ){
				if(ME.length>0){
					var sql_token="UPDATE  messages  SET status=?, message_id=? where id=?";
					con.query(sql_token, [1,ME[0].id.id,res[i].id],function(err,res,field){
					if(err){
						console.log(err);
					}else{

					}
				});
				}
			}else{
				var sql_token="UPDATE  messages  SET status=?, message_id=? where id=?";
				con.query(sql_token, [1,ME.id.id,res[i].id],function(err,res,field){
					if(err){
						console.log(err);
					}else{

					}
				});
			}

			
		}

		i-=1;
		count_message-=1;
		if(i){
			 setTimeout(function(){
				 sending_message(res,i--)
			},interval_time_message);
		}
	}else{
		count_message=0;
	}
	return 1;

		
}

async function listed_messages(){
		var sql_token="select * from  messages  where app_id=? and  status =? and created_at < ? order by message_type  limit 100";
		con.query(sql_token, [app_.app_id,0,new Date()],function(err,res,field){
			if(err){
				console.log(err);
				cont_message=0;
			}else{
				count_message=res.length??0;

				var i=0;
				if(count_message){
					time_in=(count_message/3*100);
					if(time_in<1000){
						time_in=1000;
					}
						interval_time_message=
					 sending_message(res,count_message-1);
				}

			}
		});
}

var mes_interval=null;
client.on('ready', () => {
    console.log('whatapp  - ' + client.info.me.user + ' sudah siap!');

	let patokan_error = 0;
	STATUS_APP.wa_status=5;
    STATUS_APP.wa_token=null;
    STATUS_APP.wa_number=client.info.me.user;
	UpdateStatus();
	var attemp=0;
	mes_interval=setInterval(function(){
		if(STATUS_APP.wa_status!=5){
			clearInterval(mes_interval);
		}
		if(count_message<=0){
				
			 	listed_messages();
		}
		 attemp++;
		console.log(attemp,'coba cari pesan');
	},
	interval_time_message_listed);

	
});

client.on('message', async msg => {
	console.log('Pesan Masuk: ', msg);
	
});

client.on('message_create', (msg) => {
    if(msg.fromMe) {
        console.log('Pesan Keluar: ', msg);
		
    }
});