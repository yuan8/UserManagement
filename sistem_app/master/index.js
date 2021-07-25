const fs = require('fs');
const qrcode = require('qrcode-terminal');
const mysql = require('mysql');
const unicodestring = require('unicodechar-string');
const { Client, MessageMedia } = require('whatsapp-web.js');

var con = mysql.createConnection({
	host: "localhost",
	user: "root",
	password: "uba2013?",
	database: "webapi",
	port: 3306
});

con.connect(function(err) {
	if(err) throw err;
	console.log("Database Connected!");
});

const SESSION_FILE_PATH = './session.json';
let sessionCfg;
if(fs.existsSync(SESSION_FILE_PATH)) {
    sessionCfg = require(SESSION_FILE_PATH);
}

//GUI pakai ini
//const client = new Client({ puppeteer: { headless: false, executablePath: 'C:/Program Files/Google/Chrome/Application/chrome.exe', }, session: sessionCfg });

//CLI hemat daya pakai ini
const client = new Client({
	restartOnAuthFail: true,
	puppeteer: {
		headless: false,
		//executablePath: 'C:/Program Files (x86)/Google/Chrome/Application/chrome.exe', //Windows x86
		//executablePath: 'C:/Program Files/Google/Chrome/Application/chrome.exe', //Windows x64
		//executablePath: '/usr/bin/google-chrome-stable', //Linux
		//executablePath: '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome', //Mac OS
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

//CLI normal pakai ini
//const client = new Client({ session: sessionCfg }); 

client.initialize();

client.on('qr', (qr) => {
	qrcode.generate(qr, {small: true});
	con.query(sql13, function (err13, result13, fields13) {
		if(typeof result13 !== 'undefined') {
			if(result13.length>0) {
				var sql14 = "UPDATE apps SET nama_perangkat=?, wabrowserid_perangkat=?, wasecretbundle_perangkat=?, watoken1_perangkat=?, watoken2_perangkat=?, date_perangkat=NOW() LIMIT 1";
				con.query(sql14, [qr, '', '', '', '']);
			} else {
				var sql14 = "INSERT INTO app VALUES (NULL, ?, ?, ?, ?, ?, '', '1', 1, NOW())";
				con.query(sql14, [qr, '', '', '', '']);
			}
		}
	});
});

client.on('authenticated', (session) => {
    console.log('Terotentikasi: ', session);
    sessionCfg=session;
    fs.writeFile(SESSION_FILE_PATH, JSON.stringify(session), function (err,success) {
        if(err) {
            console.error(err);
        }
        console.log('success',success);
    	var sql13 = "SELECT * FROM perangkat LIMIT 1";
		con.query(sql13, function (err13, result13, fields13) {
			if(typeof result13 !== 'undefined') {
				if(result13.length>0) {
					var sql14 = "UPDATE perangkat SET nama_perangkat=?, wabrowserid_perangkat=?, wasecretbundle_perangkat=?, watoken1_perangkat=?, watoken2_perangkat=?, date_perangkat=NOW() LIMIT 1";
					con.query(sql14, ['TERSAMBUNG', session['WABrowserId'], session['WASecretBundle'], session['WAToken1'], session['WAToken2']]);
				} else {
					var sql14 = "INSERT INTO perangkat VALUES (NULL, ?, ?, ?, ?, ?, '', '1', 1, NOW())";
					con.query(sql14, ['TERSAMBUNG', session['WABrowserId'], session['WASecretBundle'], session['WAToken1'], session['WAToken2']]);
				}
			}
		});
    });
});

client.on('auth_failure', msg => {
    console.error('Otentikasi Gagal: ', msg);
});

client.on('disconnected', (reason) => {
    console.log('Klien sudah keluar: ', reason);
    client.destroy();
    console.log('File '+SESSION_FILE_PATH+' berhasil dihapus');
    client.initialize();
    console.log('Mohon tunggu, sedang proses cetak QR Code BARU...');
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
	console.log(formatted);
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

client.on('ready', () => {
    console.log('WApiKu 3.1.4 - ' + client.info.me.user + ' sudah siap!');
	let patokan_error = 0;
	var detik = 5, jeda = detik * 1000;
	setInterval(function() {
		var sql = "SELECT id_kirim, nomor_tujuan_kirim, waktu_kirim, pesan_kirim, id_jenis_kirim, gambar_kirim, file_kirim FROM kirim WHERE status_kirim='1' AND waktu_kirim<=NOW() AND (nomor_pengirim_kirim=? OR nomor_pengirim_kirim='*') AND nomor_tujuan_kirim<>? ORDER BY id_kirim ASC LIMIT 1";
		con.query(sql, [client.info.me.user, client.info.me.user], async function (err, result, fields) {
			if(typeof result !== 'undefined') {
				if(result.length>0) {
					var sql2 = "UPDATE kirim SET status_kirim=?, date_kirim=NOW() WHERE id_kirim=?";
					var nomor_tujuan_kirim = phoneNumberFormatter(result[0].nomor_tujuan_kirim);
					const isRegisteredNumber = await checkRegisteredNumber(nomor_tujuan_kirim);
					if(isRegisteredNumber || nomor_tujuan_kirim.endsWith('@g.us')) {
						con.query(sql2, ['2', result[0].id_kirim]);
						if(result[0].id_jenis_kirim==4) {
							await client.sendMessage(nomor_tujuan_kirim, unicodestring(result[0].pesan_kirim));
							console.log('Kirim Pesan ke ' + nomor_tujuan_kirim.slice(0, -5) + ' telah terkirim');
						} else if(result[0].id_jenis_kirim==5) {
							const media = await MessageMedia.fromFilePath('./uploads/' + result[0].gambar_kirim);
							await client.sendMessage(nomor_tujuan_kirim, media, { caption: unicodestring(result[0].pesan_kirim) });
							console.log('Kirim Gambar & Pesan ke ' + nomor_tujuan_kirim.slice(0, -5) + ' telah terkirim');
						} else if(result[0].id_jenis_kirim==6) {
							const media = await MessageMedia.fromFilePath('./uploads/' + result[0].file_kirim);
							await client.sendMessage(nomor_tujuan_kirim, media);
							console.log('Kirim Berkas ke ' + nomor_tujuan_kirim.slice(0, -5) + ' telah terkirim');
						}
					} else {
						con.query(sql2, ['3', result[0].id_kirim]);
						console.log('Nomor tidak terdaftar di WhatsApp');
					}
				} else {
					//console.log('Tidak ada antrian dengan status_kirim '1' & waktu kirim dibawah waktu sekarang NOW() MySQL');
				}
			}
		});
		var sql13x = "SELECT * FROM perangkat";
		con.query(sql13x, function (err13x, result13x, fields13x) {
			if(typeof result13x !== 'undefined') {
				if(result13x.length>0) {
					//console.log('Masih Ada Data Perangkat');
					var sql20 = "UPDATE perangkat SET keterangan_perangkat=? WHERE id_perangkat=?";
					con.query(sql20, [client.info.me.user, result13x[0].id_perangkat]);
				} else {
					if(patokan_error==0) {
						console.log('Inisialisasi ulang...');
						client.destroy();
						console.log('File session.js berhasil dihapus');
						client.initialize();
						patokan_error = 1;
					}
					console.log('Mohon tunggu, sedang proses cetak QR Code BARU...');
				}
			}
		});
	}, jeda);
});

client.on('message', async msg => {
	console.log('Pesan Masuk: ', msg);
	var sql3 = "INSERT INTO `message`(`mediakey_message`, `idfromme_message`, `idremote_message`, `idid_message`, `id_serialized_message`, `ack_message`, `hasmedia_message`, `body_message`, `type_message`, `timestamp_message`, `from_message`, `to_message`, `author_message`, `isforwarded_message`, `isstatus_message`, `isstarred_message`, `broadcast_message`, `fromme_message`, `hasquotedmsg_message`, `location_message`, `vcard_message`, `mentionedids_message`, `link_message`, `keterangan_message`, `status_message`, `user_message`, `date_message`) VALUES (?,?,?,?,\"?\",?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?,\"?\", 'PESAN MASUK', '1', 1, NOW())";
	con.query(sql3, [msg.mediaKey, msg.id['fromMe'], msg.id['remote'], msg.id['id'], msg.id['_serialized'], msg.ack, msg.hasMedia, convertEMOJI(msg.body), msg.type, msg.timestamp, msg.from, msg.to, msg.author, msg.isForwarded, msg.isStatus, msg.isStarred, msg.broadcast, msg.fromMe, msg.hasQuoteMsg, msg.location, msg.vCard, msg.mentionIds, msg.links]);
	
	let chat = await msg.getChat();
	chat.sendSeen();

	var sql16 = "SELECT * FROM kontak WHERE telepon_kontak=? LIMIT 1";
	con.query(sql16, [msg.from.slice(0, -5)], async function (err16, result16, fields16) {
		if(typeof result16 !== 'undefined') {
			if(result16.length>0) {
				//console.log('Nomor Telepon sudah ada pada tabel Kontak');
				let id_kontak = await result16[0].id_kontak;
				let nama_kontak = await result16[0].nama_kontak;
				let alamat_kontak = await result16[0].alamat_kontak;
				let kota_kontak = await result16[0].kota_kontak;
				let telepon_kontak = await result16[0].telepon_kontak;
				if(msg.body.toLowerCase() === 'hai' || msg.body.toLowerCase() === 'hi' || msg.body.toLowerCase() === 'hy' || msg.body.toLowerCase() === 'hii' || msg.body.toLowerCase() === 'hay' || msg.body.toLowerCase() === 'tes' || msg.body.toLowerCase() === 'test' || msg.body.toLowerCase() === 'halo') {
					await msg.reply(msg.body + ' juga *' + ((nama_kontak=='')?msg.from.slice(0, -5):nama_kontak) + '*, Selamat datang di WApiKu 3.1.4 - Unofficial WhatsApp API Gateway');
				}
			} else {
				//console.log('Nomor Telepon belum ada pada tabel Kontak');
				var sql14 = "INSERT INTO kontak VALUES (NULL, 1, '', '', '', ?, 0, '', '1', 1, NOW())";
				con.query(sql14, [msg.from.slice(0, -5)]);
				if(msg.body.toLowerCase() === 'hai' || msg.body.toLowerCase() === 'hi' || msg.body.toLowerCase() === 'hy' || msg.body.toLowerCase() === 'hii' || msg.body.toLowerCase() === 'hay' || msg.body.toLowerCase() === 'tes' || msg.body.toLowerCase() === 'test' || msg.body.toLowerCase() === 'halo') {
					await msg.reply(msg.body + ' juga *' + msg.from.slice(0, -5) + '*, Selamat datang di WApiKu 3.1.4 - Unofficial WhatsApp API Gateway');
				}
			}
		}
	});

	var sql11 = "SELECT id_perintah, nama_perintah, pilihan_perintah, acak_perintah, keterangan_perintah FROM perintah WHERE nama_perintah=? AND status_perintah='1' LIMIT 1";
	con.query(sql11, [convertEMOJI(msg.body)], function (err11, result11, fields11) {
		if(typeof result11 !== 'undefined') {
			if(result11.length>0) {
				//client.sendMessage(msg.from, unicodestring(result11[0].respon_perintah));
				if(result11[0].pilihan_perintah=='1') {
					var sql10 = "SELECT * FROM perintah_detail WHERE id_perintah=? AND status_perintah_detail='1'";
					con.query(sql10, [result11[0].id_perintah], function (err10, result10, fields10) {
						if(typeof result10 !== 'undefined') {
							if(result10.length>0) {
								if(result11[0].acak_perintah=='1') {
									client.sendMessage(msg.from, unicodestring(result10[getRndInteger(0,result10.length)].keterangan_perintah_detail));
									console.log('Perintah Detail Acak');
								} else {
									client.sendMessage(msg.from, unicodestring(result10[0].keterangan_perintah_detail));
									console.log('Perintah Detail Pertama, atau dimainkan field kondisi_perintah_detail disini');
								}
							} else {
								console.log('Perintah Detail tidak ditemukan');
							}
						} else {
							console.log('Query Error: ' + sql10);
						}
					});
				} else {
					client.sendMessage(msg.from, unicodestring(result11[0].keterangan_perintah));
				}
			} else {
				//console.log('Perintah tidak ditemukan');
			}
		}
	});

	if(msg.body.toLowerCase() === '!info') {
        let info = client.info;
        client.sendMessage(msg.from, `*Info Perangkat*
Nama Akun: ${info.pushname}
Nomor HP: ${info.me.user}
Platform: ${info.platform}
Versi WhatsApp: ${info.phone.wa_version}`);
    } else if(msg.body.toLowerCase().startsWith('!kirimke ')) {
        let number = msg.body.split(' ')[1];
        let messageIndex = msg.body.indexOf(number) + number.length;
        let message = msg.body.slice(messageIndex, msg.body.length);
        client.sendMessage(phoneNumberFormatter(number), unicodestring(message) + "\n\n *Meneruskan pesan dari " + msg.from.slice(0, -5) + " menggunakan fitur Forward Message*");
	} else if (msg.body == '!groups') {
		client.getChats().then(chats => {
			const groups = chats.filter(chat => chat.isGroup);

			if (groups.length == 0) {
				msg.reply('Anda tidak mempunyai Grup WhatsApp');
			} else {
				var sql33 = "DELETE FROM grup";
				con.query(sql33);
				groups.forEach((group, i) => {
					var sql34 = "INSERT INTO grup VALUES (NULL, ?, ?, '1', 1, NOW())";
					con.query(sql34, [`${group.id._serialized}`, `${group.name}`]);
				});
				msg.reply('Database Grup WhatsApp sudah diperbarui');
			}
		});
	}
	
	if(msg.hasMedia) {
		const attachmentData = await msg.downloadMedia();
		/*
		msg.reply(`*Media info*
MimeType: ${attachmentData.mimetype}
Filename: ${attachmentData.filename}
Data (length): ${attachmentData.data.length}`);
		*/
		var extension_file = attachmentData.mimetype.split("/");
		if(extension_file[1].trim() == 'octet-stream') {
			extension_file = attachmentData.filename.split(".");
		}
		const file_ke_base64 = await Buffer.from(attachmentData.data,'base64');
		await fs.writeFileSync('./downloads/' + client.info.me.user + '_' + (new Date()).getTime() + '.' + extension_file[1], file_ke_base64); //base64_ke_file
	}
});

client.on('message_create', (msg) => {
    if(msg.fromMe) {
        console.log('Pesan Keluar: ', msg);
		var sql4 = "INSERT INTO `message`(`mediakey_message`, `idfromme_message`, `idremote_message`, `idid_message`, `id_serialized_message`, `ack_message`, `hasmedia_message`, `body_message`, `type_message`, `timestamp_message`, `from_message`, `to_message`, `author_message`, `isforwarded_message`, `isstatus_message`, `isstarred_message`, `broadcast_message`, `fromme_message`, `hasquotedmsg_message`, `location_message`, `vcard_message`, `mentionedids_message`, `link_message`, `keterangan_message`, `status_message`, `user_message`, `date_message`) VALUES (?,?,?,?,\"?\",?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?,\"?\", 'PESAN KELUAR', '1', 1, NOW())";
		con.query(sql4, [msg.mediaKey, msg.id['fromMe'], msg.id['remote'], msg.id['id'], msg.id['_serialized'], msg.ack, msg.hasMedia, convertEMOJI(msg.body), msg.type, msg.timestamp, msg.from, msg.to, msg.author, msg.isForwarded, msg.isStatus, msg.isStarred, msg.broadcast, msg.fromMe, msg.hasQuoteMsg, msg.location, msg.vCard, msg.mentionIds, msg.links]);
    }
});