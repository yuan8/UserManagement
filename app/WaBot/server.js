const pm2 = require('pm2')
const redis = require("redis");


var red_option={
    host:'127.0.0.1',
    port:6379,
    db:0
}
const red = redis.createClient(red_option);

red.on('subscribe',(chanel,count)=>{
    console.log('subscribe chanel '+ chanel+' : '+count );
});
var red_prefix='leon_database_';

var red_app_method=red_prefix+'server';


red.subscribe(red_app_method);


pm2.connect(function(err) {
  if (err) {
    console.error(err)
    process.exit(2)
  }


  red.on('message',(chanel,mes)=>{
    mes=JSON.parse(mes);
    console.log(mes);
    switch(mes.method){
      case 'start':
        pm2.stop(mes.app.name, function(err, apps) {
           if(err){
             
           }else{
             pm2.delete(mes.app.name,function(err,proc){
             });

           }
         });

         setTimeout(function(){
           pm2.start(mes.app, function(err, apps) {
           if(err){
              pm2.list((err, list) => {
                console.log('listing app - try start '+mes.app.name,list);
                pm2.restart(mes.app.name, (err, proc) => {
                   if(err){
                      pm2.delete(mes.app.name,function(err,proc){
                      
                      });
                   }
                })
              });
           }
         });
         },2000);

      break;
      case 'stop':

      pm2.stop(mes.app.name, function(err, apps) {
           if(err){
              pm2.list((err, list) => {
                console.log('listing app - try stop '+mes.app.name,list);
              });
           }
         });

      break;
      case 'delete':

      pm2.delete(mes.app.name,function(err,proc){
              
      });


      break;
     default:
       console.log(mes.method,'not found proccess');
     break;


    }

  });
});


