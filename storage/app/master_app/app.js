

const qrcode = require('qrcode-terminal');
const { Client,Chat,GroupChat } = require('whatsapp-web.js');
const fs = require('fs');

const mysql = require('mysql');
const unicodestring = require('unicodechar-string');
const https = require('https')
var spawn = require('child_process').spawn;

var con = mysql.createConnection({
    host: "localhost",
    user: "root",
    password: "uba2013?",
    database: "whatapp_api",
    port: 3306
});

var app_={
    app_id:1
}

var red_option={
    host:'127.0.0.1',
    port:6379,
    db:0
}

const redis = require("redis");
const red = redis.createClient(red_option);
const redServer = redis.createClient(red_option);

var red_prefix='wabot_database_';




red.on("error", function(error) {
  console.error(error);
});
  



   



con.connect(function(err) {
    if(err) throw err;
    console.log("Database Connected!");
});

const SESSION_FILE_PATH=the_dirname+'/app.session.json';

var sessionCfg;
if(fs.existsSync(SESSION_FILE_PATH)) {
    sessionCfg = require(SESSION_FILE_PATH);
}else{
    sessionCfg=undefined;
}

const STATUS_APP_PATH=the_dirname+'/app.status.json';

process.on('beforeExit', (code) => {
  console.log('Process beforeExit event with code: ', code);
});

process.on('exit', (code) => {
  console.log('Process exit event with code: ', code);
});


var ClientOption={
    restartOnAuthFail: true,
    puppeteer: {
        headless: false,
        takeoverOnConflict:false,
        //executablePath: 'C:/Program Files (x86)/Google/Chrome/Application/chrome.exe', //Windows x86
        //executablePath: 'C:/Program Files/Google/Chrome/Application/chrome.exe', //Windows x64
        //executablePath: '/usr/bin/google-chrome-stable', //Linux
        //executablePath: '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome', //Mac OS
        // executablePath: '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
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
    session: sessionCfg,
};





var client = new Client(ClientOption);

process.on('unhandledRejection', (reason, p) => {
    redServer.publish(red_prefix+'server',JSON.stringify({
        'method':'start',
        'app':{
            'script':the_dirname+'/app.js',
            'name':app_data.name
            }
        }
    ));
});

var def_status={
    'browser_open':false,
    'wa_state':null,
    'pid_process':null,
    'status_client':null,
    'qr_login':null,
    'pid':null,
    'user':{},
    'app_id':app_data.id,
    'qr_code':null,
    'updated_at':null
};


var status=def_status;

var old_status={
     'browser_open':false,
    'wa_state':null,
    'pid_process':null,
    'status_client':null,
};

function set_status(){
        console.log('------',status.status_client,'--------');
       fs.writeFile(STATUS_APP_PATH, JSON.stringify(status), function (err,success) {
                if(err){
                    console.log('status file err',err);
                }else{
                        console.log(STATUS_APP_PATH,status);

                }

                setTimeout(()=>{
                    set_status();
                },1000);
            });

     
};

set_status();

var index_message=0;
var data_message=[];



function phoneNumberFormatter(number) {
    number=number.replace('+','');
    number=number.replace(/-/g,'');
    number=number.replace(/ /g,'');
    number=number.replace("@g.us",'');
    number=number.replace("@c.us",'');


    let formatted = number+'';
    console.log(!String.prototype.startsWith,formatted);
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

var interval_message_time=800;
var success_message=0;
var count_check_call=0;

async function listed_contact(){
    var contact=[];
    var group=[];

    if(client.pupBrowser.isConnected() && (status.status_client=='READY')){
        var wa_contact= await client.getContacts();
        for(var i in wa_contact){
          if(wa_contact[i].isGroup==false){
                contact.push({
                    'wa_id_server':wa_contact[i].id.server,
                    'wa_id_user':wa_contact[i].id.user,
                    'wa_name':wa_contact[i].name,
                    'wa_push_name':wa_contact[i].pushname,
                    'wa_verified_name':wa_contact[i].verifiedName,
                    'wa_id_serialized':wa_contact[i].id._serialized,
                    'wa_id_bussiness':wa_contact[i].isBusiness,
                    'wa_number':wa_contact[i].number,wa_contact,
                    'wa_is_group':wa_contact[i].isGroup,
                    'wa_contact':wa_contact[i].isWAContact,
                    'wa_phone_in_contact':wa_contact[i].isMyContact,
                    'wa_phone_blocked':wa_contact[i].isBlocked,
                    'wa_group_contact':[]
                });
            }else{
                 group.push({
                    'wa_id_server':wa_contact[i].id.server,
                    'wa_id_user':wa_contact[i].id.user,
                    'wa_name':wa_contact[i].name,
                    'wa_push_name':wa_contact[i].pushname,
                    'wa_verified_name':wa_contact[i].verifiedName,
                    'wa_id_serialized':wa_contact[i].id._serialized,
                    'wa_id_bussiness':wa_contact[i].isBusiness,
                    'wa_number':wa_contact[i].number,wa_contact,
                    'wa_is_group':wa_contact[i].isGroup,
                    'wa_contact':wa_contact[i].isWAContact,
                    'wa_phone_in_contact':wa_contact[i].isMyContact,
                    'wa_phone_blocked':wa_contact[i].isBlocked,
                    'wa_group_contact':[]
                });
            }

        }
    }

}

async function send_message(){

    interval_message_time=800;
    if(data_message[index_message]!==undefined){
       if(client.pupBrowser){
            if(client.pupBrowser.isConnected() && (status.status_client=='READY')){

            if(index_message<data_message.length){
                console.log('try sending message to',phoneNumberFormatter(data_message[index_message].to));
              try{
                var status_send =await client.sendMessage(phoneNumberFormatter(data_message[index_message].to),unicodestring(data_message[index_message].content)).catch(er=>{
                   if(er){
                       console.log('err_send message',er);
                   }
                    
                });

                if(status_send){
                     var get_mess_sql="update  messages set status=?, updated_at=? where id=?";
                    con.query(get_mess_sql, [true,new Date(),data_message[index_message].id],function(err,res,field){
                        
                    });
                }
                console.log('status send ',status_send);
              
               success_message+=1;
               if((index_message%50)==0){
                   interval_message_time=4000;
               }

              }catch(err){
                  console.log('pack send message',err);

              }

               setTimeout(()=>{
                     index_message+=1;
                    send_message();
                },interval_message_time);
               

            }else{

                setTimeout(()=>{
                        check_message();
                },1000);
            }
        }else if(!client.pupBrowser.isConnected()){
            init();
        }else{
             setTimeout(()=>{
                        check_message();
              },1000);
        }  
       }

    }else{

          setTimeout(()=>{
                check_message();
        },1000);   
    }
}

async function check_message(){
    index_message=0;
    data_message=[];



    if(client.pupBrowser && (status.status_client=='READY')){
        if((count_check_call%50)==0){
            var pp= await client.getProfilePicUrl(client.info.me._serialized);
            if(pp){
                if(pp.startsWith('https')){
                    const file = fs.createWriteStream(__dirname+'/downloads/pp-'+client.info.me.user+'.jpg');
                    const request = https.get(pp, function(response) {
                          response.pipe(file);
                    });
                }
            }

            

        }
    }



    var get_mess_sql="select * from  messages  where app_id=? and  status =? and send_date < ? order by message_type  limit 100";
    con.query(get_mess_sql, [app_.app_id,0,new Date()],function(err,res,field){
        if(err){
            console.log(err);
        }else{
            for(var datai in res){
                data_message.push({
                    'to':phoneNumberFormatter(res[datai].to_number),
                    'content':res[datai].content_text,
                    'id':res[datai].id

                })
            }
        }
    });

    if(count_check_call>=1000){
        init();
    }else if(success_message>200){
        setTimeout(function(){
            send_message();
        },(success_message/200)+5000);
    }else{

        setTimeout(function(){
            send_message();
        },800);

    }
    count_check_call+=1;



}


var interval_message;

var RTIMEOUT;

client.on('qr', qr => {
    status.status_client='LOGIN QR';
    status.qr_login=qr;
    qrcode.generate(qr, {small: true});
});

client.on('authenticated', async (session) => {
    sessionCfg=session;
    status.status_client='AUTHENTICATED';
    status.qr_code=null;
        fs.writeFile(SESSION_FILE_PATH, JSON.stringify(session), function (err,success) {
            if(err) {
                console.error(err);
            }else{
                console.log('saving session');
            }
        });
    });

client.on('ready', () => {

    status.status_client='READY';
    status.user=client.info.me;

    status.qr_login=null;

    console.log('READY');
    console.log('Client is ready!',client.info.me.user);

    check_message();
    listed_contact();

});

client.on('disconnected', reason => {
    status.status_client='DISCONNECTED';
    status.qr_login=null;
       
    
});

var clear_state;

client.on('change_state',state=>{

    if(status.wa_state!=state){
        status.wa_state=state;
        if(clear_state!=undefined){
            clearTimeout(clear_state);
            clear_state=undefined;
        }

        setTimeout(()=>{
            status.wa_state=null;
        },3000);

    }

    if(state=='CONFLICT'){
        setTimeout(()=>{
            if(status.wa_state=='CONFLICT'){
                init();
            }
        },10000);
    }


    

});

client.on('auth_failure',state=>{
    status.qr_login=null;
    status.status_client='AUTH FAIL';
});

client.on('message', message => {
    if(message.body === '!ping') {
        message.reply('pong');
    }

    if(message.body === '!ping') {
        client.sendMessage(message.from, 'pong');
    }
});


var count_init=0;
var check_sum;
var old_red_client=null;
 
async function init(){
   
    status.status_client='TRY CONNECTION';
    status.qr_login=null;
    if(client._qrRefreshInterval!=undefined){
          clearInterval(client._qrRefreshInterval);
          client._qrRefreshInterval = undefined;
    }

    if(client.pupBrowser){
        ClientOption=client.options;
        client.pupBrowser.disconnect();
        client.pupBrowser.close();
    }

    

    count_init+=1;

    setTimeout(async ()=>{
             try {
                 console.log('try wakeup');
                 client.options=ClientOption;
                 client.initialize();
                 console.log('interval',client._qrRefreshInterval);
         } catch (err) {
             init();
         }
     },1000);
    console.log('init done');
}

init();




function restart_server(){
     var COUNT_REST=0;
      
    if(client._qrRefreshInterval!==undefined){
          clearInterval(client._qrRefreshInterval);
          client._qrRefreshInterval=undefined;
     }
            
     if(process.env.process_restarting) {
            COUNT_REST=process.env.process_restarting;
     }
      

    console.log('PRIPARING RESTART SERVER');
    client.status_client='PRIPARING RESTART SERVER';

    console.log(process.argv,process.argv.slice(1));

    setTimeout(()=>{
          spawn(process.argv[0], process.argv.slice(1), {
            env: { process_restarting: COUNT_REST+1 },
            stdio: 'ignore'
          }).unref();
      },3000);
}













