// BYOB 顧客推薦表單處理功能（獨立版本）
// 設定常數
var WORDPRESS_API_URL = 'https://byobmap.com/wp-json/byob/v1/restaurant';
var API_KEY = 'byob-secret-key-2025';
var NOTIFICATION_EMAIL = 'byobmap.tw@gmail.com';

// 主要函數：處理顧客推薦表單提交並發送到 WordPress
function onCustomerFormSubmit(e) {
  try {
    Logger.log('=== 開始處理顧客推薦表單提交 ===');
    
    // 解析表單資料
    var formData = parseCustomerFormData();
    Logger.log('解析的顧客推薦資料:');
    Logger.log(JSON.stringify(formData, null, 2));
    
    // 檢查資料是否為空
    if (!formData || Object.keys(formData).length === 0) {
      throw new Error('解析的顧客推薦資料為空，請檢查試算表結構');
    }
    
    // 發送到 WordPress
    var result = sendToCustomerWordPress(formData);
    Logger.log('WordPress API 回應:');
    Logger.log(JSON.stringify(result, null, 2));
    
    // 發送成功通知郵件
    sendCustomerNotificationEmail(formData, result);
    
    Logger.log('顧客推薦表單處理完成');
    
  } catch (error) {
    Logger.log('顧客推薦表單處理發生錯誤: ' + error.toString());
    sendErrorNotification(error);
  }
}

// 解析顧客推薦表單資料（基於純淨版結構）
function parseCustomerFormData() {
  try {
    Logger.log('=== 開始解析顧客推薦表單資料 ===');
    
    var spreadsheet = SpreadsheetApp.getActiveSpreadsheet();
    var formSheet = spreadsheet.getSheets()[0]; // 表單回應工作表
    var mappingSheet = spreadsheet.getSheetByName("欄位設定表");
    
    if (!mappingSheet) {
      throw new Error('找不到「欄位設定表」工作表');
    }
    
    // 取得最新一筆表單資料
    var formData = formSheet.getDataRange().getValues();
    var formHeaders = formData[0];
    var lastFormRow = formData[formData.length - 1];
    
    Logger.log('表單標題行:');
    Logger.log(formHeaders);
    Logger.log('最新顧客推薦資料:');
    Logger.log(lastFormRow);
    
    // 取得欄位映射設定
    var mappingData = mappingSheet.getDataRange().getValues();
    Logger.log('欄位設定表資料:');
    Logger.log(mappingData);
    
    // 建立表單標題索引
    var headerIndex = {};
    for (var h = 0; h < formHeaders.length; h++) {
      if (formHeaders[h]) {
        var cleanHeader = toHalfWidth(String(formHeaders[h]).trim());
        headerIndex[cleanHeader] = h;
        Logger.log('標題索引: "' + formHeaders[h] + '" -> "' + cleanHeader + '" -> 索引 ' + h);
      }
    }
    
    // 根據欄位設定表解析資料
    var parsedData = {};
    
    // 跳過標題行，從第二行開始處理映射
    for (var i = 1; i < mappingData.length; i++) {
      var mapping = mappingData[i];
      var wordpressField = mapping[0]; // WordPress 欄位名稱
      var formFieldName = mapping[1];  // 表單欄位名稱
      
      if (!wordpressField || !formFieldName) {
        continue; // 跳過空行
      }
      
      var cleanFormFieldName = toHalfWidth(String(formFieldName).trim());
      var formFieldIndex = headerIndex[cleanFormFieldName];
      
      if (formFieldIndex !== undefined) {
        var value = lastFormRow[formFieldIndex];
        
        // 特殊處理某些欄位
        if (wordpressField === 'is_charged') {
          // 轉換開瓶費選項
          if (String(value).indexOf('酌收') !== -1) {
            parsedData[wordpressField] = '酌收';
          } else if (String(value).indexOf('不收') !== -1) {
            parsedData[wordpressField] = '不收費';
          } else if (String(value).indexOf('其他') !== -1) {
            parsedData[wordpressField] = '其他';
          } else {
            parsedData[wordpressField] = value || '';
          }
        } else if (wordpressField === 'restaurant_type') {
          // 特殊處理餐廳類型，使用「排除法」識別「其他」內容
          var restaurantTypes = String(value || '');
          Logger.log('🔍 處理餐廳類型: "' + restaurantTypes + '"');
          
          // 防護機制：檢查是否已經處理過
          if (parsedData.hasOwnProperty('restaurant_type')) {
            Logger.log('⚠️ 餐廳類型已經處理過，跳過重複處理');
            continue;
          }
          
          // 檢查是否為空或空白
          if (!restaurantTypes || restaurantTypes.trim() === '') {
            Logger.log('⚠️ 餐廳類型為空，跳過處理');
            continue;
          }
          
          // 已知的餐廳類型清單
          var knownTypes = [
            '台式', '法式', '義式', '日式', '美式', '熱炒', '小酒館', '咖啡廳', 
            '私廚', '異國料理', '燒烤', '火鍋', '牛排', 'Lounge Bar', 'Buffet', 'Fine dining'
          ];
          
          // 分割餐廳類型
          var typesArray = restaurantTypes.split(',').map(function(type) {
            return type.trim();
          }).filter(function(type) {
            return type.length > 0;
          });
          Logger.log('📋 分割後的類型陣列: [' + typesArray.join(', ') + ']');
          
          // 使用「排除法」識別「其他」內容
          var validTypes = [];
          var otherNote = '';
          var hasOther = false;
          
          for (var j = 0; j < typesArray.length; j++) {
            var type = typesArray[j];
            
            if (knownTypes.includes(type) || type === '其他') {
              validTypes.push(type);
              if (type === '其他') {
                hasOther = true;
              }
              Logger.log('✅ 識別到已知類型: "' + type + '"');
            } else {
              otherNote = type;
              Logger.log('🔍 識別到未知類型，可能是「其他」說明: "' + type + '"');
            }
          }
          
          // 處理結果
          if (otherNote && hasOther) {
            parsedData[wordpressField] = validTypes.join(', ');
            parsedData['restaurant_type_other_note'] = otherNote;
            Logger.log('✅ 已設定 restaurant_type_other_note = "' + otherNote + '"');
          } else if (otherNote && !hasOther) {
            validTypes.push('其他');
            parsedData[wordpressField] = validTypes.join(', ');
            parsedData['restaurant_type_other_note'] = otherNote;
            Logger.log('✅ 已設定 restaurant_type_other_note = "' + otherNote + '"');
          } else {
            parsedData[wordpressField] = validTypes.join(', ');
          }
          
          Logger.log('🏷️ 最終餐廳類型: "' + parsedData[wordpressField] + '"');
        } else if (wordpressField === 'restaurant_type_other_note') {
          // 跳過 restaurant_type_other_note 的欄位映射，因為它由餐廳類型邏輯自動生成
          Logger.log('⏭️ 跳過 restaurant_type_other_note 的欄位映射，因為它由餐廳類型邏輯自動生成');
          continue;
        } else if (wordpressField === 'equipment_other_note') {
          // 跳過 equipment_other_note 的欄位映射，因為它由酒器設備邏輯自動生成
          Logger.log('⏭️ 跳過 equipment_other_note 的欄位映射，因為它由酒器設備邏輯自動生成');
          continue;
        } else if (wordpressField === 'equipment') {
          // 特殊處理酒器設備，使用「排除法」識別「其他」內容（類似餐廳類型）
          var equipmentValue = String(value || '');
          Logger.log('🔍 處理酒器設備: "' + equipmentValue + '"');
          
          // 防護機制：檢查是否已經處理過
          if (parsedData.hasOwnProperty('equipment')) {
            Logger.log('⚠️ 酒器設備已經處理過，跳過重複處理');
            parsedData[wordpressField] = value || '';
            Logger.log('成功映射: ' + wordpressField + ' = "' + parsedData[wordpressField] + '"');
            continue;
          }
          
          // 檢查是否為空或空白
          if (!equipmentValue || equipmentValue.trim() === '') {
            Logger.log('⚠️ 酒器設備為空，跳過處理');
            parsedData[wordpressField] = '';
            Logger.log('成功映射: ' + wordpressField + ' = "' + parsedData[wordpressField] + '"');
            continue;
          }
          
          // 已知的酒器設備清單（對應 Google 表單的選項）
          var knownEquipment = [
            '無提供', '開瓶器', '酒杯', '冰桶', '醒酒器'
          ];
          
          // 分割酒器設備（如果是多選的分隔字串）
          var equipmentArray = equipmentValue.split(',').map(function(item) {
            return item.trim();
          }).filter(function(item) {
            return item.length > 0;
          });
          Logger.log('📋 分割後的設備陣列: [' + equipmentArray.join(', ') + ']');
          
          // 使用「排除法」識別「其他」內容
          var validEquipment = [];
          var otherNotes = [];
          var hasOther = false;
          
          for (var j = 0; j < equipmentArray.length; j++) {
            var item = equipmentArray[j];
            
            if (knownEquipment.includes(item) || item === '其他') {
              validEquipment.push(item);
              if (item === '其他') {
                hasOther = true;
              }
              Logger.log('✅ 識別到已知設備: "' + item + '"');
            } else {
              // 未知項目（如「嘔吐」）歸類為「其他」說明
              otherNotes.push(item);
              Logger.log('🔍 識別到未知設備，歸類為「其他」說明: "' + item + '"');
              hasOther = true;
            }
          }
          
          // 處理結果
          if (otherNotes.length > 0) {
            // 如果有未知項目，確保包含「其他」選項
            if (!validEquipment.includes('其他')) {
              validEquipment.push('其他');
            }
            parsedData[wordpressField] = validEquipment.join(', ');
            parsedData['equipment_other_note'] = otherNotes.join(', ');
            Logger.log('✅ 自動加入「其他」選項，說明: "' + parsedData['equipment_other_note'] + '"');
          } else {
            parsedData[wordpressField] = validEquipment.join(', ');
          }
          
          Logger.log('🏷️ 最終酒器設備: "' + parsedData[wordpressField] + '"');
          if (parsedData['equipment_other_note']) {
            Logger.log('📝 酒器設備其他說明: "' + parsedData['equipment_other_note'] + '"');
          }
        } else {
          parsedData[wordpressField] = value || '';
        }
        
        Logger.log('成功映射: ' + wordpressField + ' = "' + parsedData[wordpressField] + '"');
      } else {
        Logger.log('⚠️ 找不到表單欄位: "' + formFieldName + '" -> "' + cleanFormFieldName + '"');
        parsedData[wordpressField] = '';
      }
    }
    
    // 處理餐廳名稱（直接從表單取得，不透過映射表）
    var restaurantNameIndex = headerIndex['餐廳名稱(必填)'];
    if (restaurantNameIndex !== undefined) {
      parsedData['restaurant_name'] = String(lastFormRow[restaurantNameIndex] || '').trim();
    }
    
    // 補上顧客推薦時的預設值（不覆寫推薦者欄位）
    // 使用空字串作為預設值，因為 WordPress 端已不要求這些欄位必填
    parsedData['contact_person'] = parsedData['contact_person'] || '';
    parsedData['email'] = parsedData['email'] || '';
    parsedData['district'] = parsedData['district'] || '';
    parsedData['phone'] = parsedData['phone'] || '';
    parsedData['source'] = 'customer_recommendation'; // 標記為顧客推薦來源
    // customer_recommender_name 和 customer_recommender_email 保持原本解析的值，不覆寫
    
    Logger.log('最終解析結果:');
    Logger.log(JSON.stringify(parsedData, null, 2));
    
    // 檢查核心必填欄位（只檢查 3 個絕對必要的欄位）
    var requiredFields = ['restaurant_name', 'address', 'is_charged'];
    var missingFields = [];
    
    for (var r = 0; r < requiredFields.length; r++) {
      var field = requiredFields[r];
      if (!parsedData[field] || parsedData[field] === '') {
        missingFields.push(field);
      }
    }
    
    if (missingFields.length > 0) {
      Logger.log('❌ 缺少核心必填欄位: ' + missingFields.join(', '));
      throw new Error('缺少核心必填欄位: ' + missingFields.join(', '));
    } else {
      Logger.log('✅ 所有核心必填欄位都有資料');
    }
    
    return parsedData;
    
  } catch (error) {
    Logger.log('解析顧客推薦表單資料時發生錯誤: ' + error.toString());
    throw error;
  }
}

// 全形轉半形函數
function toHalfWidth(str) {
  if (!str) return '';
  
  var result = str.replace(/[\uFF01-\uFF5E]/g, function(ch) {
    return String.fromCharCode(ch.charCodeAt(0) - 0xFEE0);
  });
  
  result = result.replace(/\u3000/g, " ");
  
  var charMap = {
    '？': '?', '：': ':', '（': '(', '）': ')', '，': ',',
    '。': '.', '！': '!', '；': ';', '、': ',', '…': '...',
    '—': '-', '－': '-'
  };
  
  for (var fullWidth in charMap) {
    if (charMap.hasOwnProperty(fullWidth)) {
      var regex = new RegExp(fullWidth, 'g');
      result = result.replace(regex, charMap[fullWidth]);
    }
  }
  
  return result.replace(/^\s+|\s+$/g, ''); // trim
}

// 發送到 WordPress
function sendToCustomerWordPress(data) {
  Logger.log('準備發送到 WordPress 的顧客推薦資料:');
  Logger.log(JSON.stringify(data, null, 2));
  
  var options = {
    'method': 'POST',
    'headers': {
      'Content-Type': 'application/json',
      'X-API-Key': API_KEY
    },
    'payload': JSON.stringify(data),
    'muteHttpExceptions': true // 取得完整錯誤回應
  };
  
  try {
    var response = UrlFetchApp.fetch(WORDPRESS_API_URL, options);
    var responseCode = response.getResponseCode();
    var responseText = response.getContentText();
    
    Logger.log('WordPress API 回應碼: ' + responseCode);
    Logger.log('WordPress API 回應內容: ' + responseText);
    
    if (responseCode === 200 || responseCode === 201) {
      return {
        success: true,
        message: '成功建立顧客推薦文章',
        response: JSON.parse(responseText)
      };
    } else {
      throw new Error('API 回應錯誤: ' + responseCode + ' - ' + responseText);
    }
    
  } catch (error) {
    Logger.log('發送到 WordPress 時發生錯誤: ' + error.toString());
    throw error;
  }
}

// 發送成功通知郵件
function sendCustomerNotificationEmail(data, result) {
  try {
    var subject = 'BYOB 新顧客推薦 - ' + (data.restaurant_name || '未知餐廳');
    var body = 
      '<h2>新顧客推薦已收到</h2>' +
      '<p><strong>餐廳名稱：</strong>' + (data.restaurant_name || '') + '</p>' +
      '<p><strong>餐廳類型：</strong>' + (data.restaurant_type || '未提供') + '</p>' +
      '<p><strong>餐廳地址：</strong>' + (data.address || '') + '</p>' +
      '<p><strong>餐廳電話：</strong>' + (data.phone || '未提供') + '</p>' +
      '<p><strong>開瓶費條件：</strong>' + (data.is_charged || '') + '</p>' +
      '<p><strong>開瓶費金額：</strong>' + (data.corkage_fee_amount || '無') + '</p>' +
      '<p><strong>開瓶費說明：</strong>' + (data.corkage_fee_note || '無') + '</p>' +
      '<p><strong>酒器設備：</strong>' + (data.equipment || '未提供') + '</p>' +
      '<p><strong>餐廳特色：</strong>' + (data.notes || '無') + '</p>' +
      '<p><strong>推薦者姓名：</strong>' + (data.customer_recommender_name || '未提供') + '</p>' +
      '<p><strong>聯絡Email：</strong>' + (data.customer_recommender_email || '未提供') + '</p>' +
      '<p><strong>處理狀態：</strong>' + (result.success ? '成功' : '失敗') + '</p>' +
      '<p><strong>訊息：</strong>' + (result.message || result.toString()) + '</p>' +
      '<hr>' +
      '<p><small>此郵件由 BYOB 顧客推薦系統發送</small></p>';
    
    GmailApp.sendEmail(NOTIFICATION_EMAIL, subject, '', {
      htmlBody: body
    });
    
    Logger.log('已發送顧客推薦通知郵件');
    
  } catch (error) {
    Logger.log('發送顧客推薦通知郵件時發生錯誤: ' + error.toString());
  }
}

// 發送錯誤通知郵件
function sendErrorNotification(error) {
  try {
    var subject = 'BYOB 顧客推薦表單處理錯誤';
    var body = 
      '<h2>顧客推薦表單處理發生錯誤</h2>' +
      '<p><strong>錯誤訊息：</strong>' + error.toString() + '</p>' +
      '<p><strong>錯誤詳情：</strong>' + (error.stack || '無詳細資訊') + '</p>' +
      '<hr>' +
      '<p><small>此郵件由 BYOB 顧客推薦系統發送</small></p>';
    
    GmailApp.sendEmail(NOTIFICATION_EMAIL, subject, '', {
      htmlBody: body
    });
    
  } catch (emailError) {
    Logger.log('發送錯誤通知郵件時發生錯誤: ' + emailError.toString());
  }
}

// 測試函數：解析最新資料
function testParseCustomerData() {
  try {
    Logger.log('=== 測試解析顧客推薦資料 ===');
    var result = parseCustomerFormData();
    Logger.log('解析結果:');
    Logger.log(JSON.stringify(result, null, 2));
    return result;
  } catch (error) {
    Logger.log('測試解析顧客推薦資料時發生錯誤: ' + error.toString());
  }
}

// 測試函數：完整流程測試
function testCustomerCompleteFlow() {
  try {
    Logger.log('=== 測試顧客推薦完整流程 ===');
    
    var formData = parseCustomerFormData();
    if (!formData || Object.keys(formData).length === 0) {
      Logger.log('❌ 資料解析失敗');
      return;
    }
    
    Logger.log('✅ 資料解析成功');
    Logger.log('發送到 WordPress...');
    
    var result = sendToCustomerWordPress(formData);
    Logger.log('WordPress 回應:');
    Logger.log(JSON.stringify(result, null, 2));
    
    if (result.success) {
      Logger.log('✅ 完整測試成功！');
    } else {
      Logger.log('❌ WordPress API 失敗');
    }
    
  } catch (error) {
    Logger.log('完整測試失敗: ' + error.toString());
  }
}

// 設定觸發器
function setupCustomerTrigger() {
  // 刪除現有觸發器
  var triggers = ScriptApp.getProjectTriggers();
  for (var t = 0; t < triggers.length; t++) {
    if (triggers[t].getHandlerFunction() === 'onCustomerFormSubmit') {
      ScriptApp.deleteTrigger(triggers[t]);
    }
  }
  
  // 建立新的表單提交觸發器
  ScriptApp.newTrigger('onCustomerFormSubmit')
    .forSpreadsheet(SpreadsheetApp.getActiveSpreadsheet())
    .onFormSubmit()
    .create();
    
  Logger.log('顧客推薦觸發器設定完成');
}
