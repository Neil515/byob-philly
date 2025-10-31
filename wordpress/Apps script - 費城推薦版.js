// BYOB 費城推薦表單處理功能（費城專用版本）
// 設定常數
var WORDPRESS_API_URL = 'https://byobmap.com/wp-json/byob/v1/philly-restaurant';
var API_KEY = 'byob-secret-key-2025';
var NOTIFICATION_EMAIL = 'byobmap.tw@gmail.com';
var PHILLY_SPREADSHEET_ID = '1b0_HfUWtZuOBNhkHWM42_afbBn3nag_5-pL-Buq6y7E';

// 主要函數：處理費城推薦表單提交並發送到 WordPress
function onPhillyFormSubmit(e) {
  try {
    Logger.log('=== 開始處理費城推薦表單提交 ===');
    
    // 解析表單資料
    var formData = parsePhillyFormData();
    Logger.log('解析的費城推薦資料:');
    Logger.log(JSON.stringify(formData, null, 2));
    
    // 檢查資料是否為空
    if (!formData || Object.keys(formData).length === 0) {
      throw new Error('解析的費城推薦資料為空，請檢查試算表結構');
    }
    
    // 發送到 WordPress
    var result = sendToPhillyWordPress(formData);
    Logger.log('WordPress API 回應:');
    Logger.log(JSON.stringify(result, null, 2));
    
    // 發送成功通知郵件
    sendPhillyNotificationEmail(formData, result);
    
    Logger.log('費城推薦表單處理完成');
    
  } catch (error) {
    Logger.log('費城推薦表單處理發生錯誤: ' + error.toString());
    sendPhillyErrorNotification(error);
  }
}

// 解析費城推薦表單資料
function parsePhillyFormData() {
  try {
    Logger.log('=== 開始解析費城推薦表單資料 ===');
    
    // 使用指定的試算表 ID
    var spreadsheet = SpreadsheetApp.openById(PHILLY_SPREADSHEET_ID);
    var formSheet = spreadsheet.getSheets()[0]; // 表單回應工作表
    var mappingSheet = spreadsheet.getSheetByName("欄位設定表");
    
    if (!mappingSheet) {
      throw new Error('找不到「欄位設定表」工作表');
    }
    
    // 取得最新一筆表單資料
    var formData = formSheet.getDataRange().getValues();
    var formHeaders = formData[0];
    var lastFormRow = formData[formData.length - 1];
    
    Logger.log('費城表單標題行:');
    Logger.log(formHeaders);
    Logger.log('最新費城推薦資料:');
    Logger.log(lastFormRow);
    
    // 取得欄位映射設定
    var mappingData = mappingSheet.getDataRange().getValues();
    Logger.log('欄位設定表資料:');
    Logger.log(mappingData);
    
    // 建立表單標題索引
    var headerIndex = {};
    for (var h = 0; h < formHeaders.length; h++) {
      if (formHeaders[h]) {
        var cleanHeader = String(formHeaders[h]).trim();
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
      
      var cleanFormFieldName = String(formFieldName).trim();
      var formFieldIndex = headerIndex[cleanFormFieldName];
      
      if (formFieldIndex !== undefined) {
        var value = lastFormRow[formFieldIndex];
        
        // 特殊處理某些欄位
        if (wordpressField === 'philly_corkage_fee') {
          // 處理開瓶費政策（手動建立的「其他」）
          var corkageValue = String(value || '');
          Logger.log('🔍 處理開瓶費政策: "' + corkageValue + '"');
          
          if (corkageValue.indexOf('Free') !== -1) {
            parsedData[wordpressField] = 'Free';
          } else if (corkageValue.indexOf('Corkage Fee') !== -1) {
            parsedData[wordpressField] = 'Corkage Fee';
          } else if (corkageValue.indexOf('Other') !== -1) {
            parsedData[wordpressField] = 'Other';
          } else {
            parsedData[wordpressField] = corkageValue || '';
          }
          
          Logger.log('✅ 開瓶費政策: "' + parsedData[wordpressField] + '"');
          
        } else if (wordpressField === 'philly_restaurant_type') {
          // 特殊處理餐廳類型，使用「排除法」識別 other 內容
          var restaurantTypes = String(value || '');
          Logger.log('🔍 處理餐廳類型: "' + restaurantTypes + '"');
          
          // 防護機制：檢查是否已經處理過
          if (parsedData.hasOwnProperty('philly_restaurant_type')) {
            Logger.log('⚠️ 餐廳類型已經處理過，跳過重複處理');
            continue;
          }
          
          // 檢查是否為空或空白
          if (!restaurantTypes || restaurantTypes.trim() === '') {
            Logger.log('⚠️ 餐廳類型為空，跳過處理');
            parsedData[wordpressField] = '';
            continue;
          }
          
          // 已知的餐廳類型清單
          var knownTypes = [
            'Italian', 'French', 'American', 'Asian', 'Mediterranean', 'Mexican', 
            'Steakhouse', 'Seafood', 'Vegetarian/Vegan', 'Thai', 'Japanese', 
            'Indian', 'Spanish', 'Fine dining'
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
            
            if (knownTypes.includes(type) || type.toLowerCase() === 'other') {
              validTypes.push(type);
              if (type.toLowerCase() === 'other') {
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
            parsedData['philly_restaurant_type_other_note'] = otherNote;
            Logger.log('✅ 已設定 philly_restaurant_type_other_note = "' + otherNote + '"');
          } else if (otherNote && !hasOther) {
            validTypes.push('other');
            parsedData[wordpressField] = validTypes.join(', ');
            parsedData['philly_restaurant_type_other_note'] = otherNote;
            Logger.log('✅ 已設定 philly_restaurant_type_other_note = "' + otherNote + '"');
          } else {
            parsedData[wordpressField] = validTypes.join(', ');
          }
          
          Logger.log('🏷️ 最終餐廳類型: "' + parsedData[wordpressField] + '"');
          
        } else if (wordpressField === 'philly_restaurant_type_other_note') {
          // 跳過 philly_restaurant_type_other_note 的欄位映射，因為它由餐廳類型邏輯自動生成
          Logger.log('⏭️ 跳過 philly_restaurant_type_other_note 的欄位映射，因為它由餐廳類型邏輯自動生成');
          continue;
          
        } else if (wordpressField === 'wine_service_equipment') {
          // 特殊處理酒器設備，使用「排除法」識別 other 內容
          var equipmentValue = String(value || '');
          Logger.log('🔍 處理酒器設備: "' + equipmentValue + '"');
          
          // 防護機制：檢查是否已經處理過
          if (parsedData.hasOwnProperty('wine_service_equipment')) {
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
          
          // 已知的酒器設備清單
          var knownEquipment = [
            'wine glasses', 'shot glasses', 'opener/corkscrew', 'decanter', 
            'Ice bucket', 'wine storage locker service', 'none'
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
            
            if (knownEquipment.includes(item) || item.toLowerCase() === 'other') {
              validEquipment.push(item);
              if (item.toLowerCase() === 'other') {
                hasOther = true;
              }
              Logger.log('✅ 識別到已知設備: "' + item + '"');
            } else {
              // 未知項目（如「其他:」後面的內容）歸類為「其他」說明
              otherNotes.push(item);
              Logger.log('🔍 識別到未知設備，歸類為「其他」說明: "' + item + '"');
              hasOther = true;
            }
          }
          
          // 處理結果
          if (otherNotes.length > 0) {
            // 如果有未知項目，確保包含 other 選項
            if (!validEquipment.map(function(i){return i.toLowerCase();}).includes('other')) {
              validEquipment.push('other');
            }
            parsedData[wordpressField] = validEquipment.join(', ');
            var otherJoin = otherNotes.join(', ');
            parsedData['philly_equipment_other_note'] = otherJoin;
            // 兼容舊鍵（台北版邏輯）：同時寫入 equipment_other_note，避免映射表殘留造成覆蓋
            parsedData['equipment_other_note'] = otherJoin;
            Logger.log('✅ 自動加入 other 選項，說明: "' + parsedData['philly_equipment_other_note'] + '"');
          } else {
            parsedData[wordpressField] = validEquipment.join(', ');
          }
          
          Logger.log('🏷️ 最終酒器設備: "' + parsedData[wordpressField] + '"');
          if (parsedData['philly_equipment_other_note']) {
            Logger.log('📝 酒器設備其他說明: "' + parsedData['philly_equipment_other_note'] + '"');
          }
          
        } else if (wordpressField === 'philly_equipment_other_note' || wordpressField === 'equipment_other_note') {
          // 跳過「其他說明」兩個鍵的映射，統一由設備邏輯自動生成
          Logger.log('⏭️ 跳過設備其他說明鍵 (' + wordpressField + ') 的映射，改由邏輯自動生成');
          continue;
          
        } else {
          parsedData[wordpressField] = value || '';
        }
        
        Logger.log('成功映射: ' + wordpressField + ' = "' + parsedData[wordpressField] + '"');
      } else {
        Logger.log('⚠️ 找不到表單欄位: "' + formFieldName + '" -> "' + cleanFormFieldName + '"');
        parsedData[wordpressField] = '';
      }
    }
    
    // 費城專用設定
    parsedData['city'] = 'Philadelphia';
    parsedData['state'] = 'PA';
    parsedData['country'] = 'USA';
    parsedData['source'] = 'philly_community_recommendation';
    parsedData['language'] = 'en';
    
    Logger.log('最終解析結果:');
    Logger.log(JSON.stringify(parsedData, null, 2));
    
    // 檢查核心必填欄位（只有 Restaurant Name 是必填）
    var requiredFields = ['restaurant_name'];
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
    Logger.log('解析費城推薦表單資料時發生錯誤: ' + error.toString());
    throw error;
  }
}


// 發送到 WordPress
function sendToPhillyWordPress(data) {
  Logger.log('準備發送到 WordPress 的費城推薦資料:');
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
        message: 'Successfully created Philadelphia BYOB restaurant post',
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
function sendPhillyNotificationEmail(data, result) {
  try {
    var subject = 'Philadelphia BYOB New Recommendation - ' + (data.restaurant_name || 'Unknown Restaurant');
    var body = 
      '<h2>New Philadelphia BYOB Recommendation Received</h2>' +
      '<p><strong>Restaurant Name:</strong> ' + (data.restaurant_name || '') + '</p>' +
      '<p><strong>Address:</strong> ' + (data.address || 'Not provided') + '</p>' +
      '<p><strong>Phone:</strong> ' + (data.phone || 'Not provided') + '</p>' +
      '<p><strong>Website:</strong> ' + (data.website || 'Not provided') + '</p>' +
      '<p><strong>Corkage Fee Policy:</strong> ' + (data.philly_corkage_fee || 'Not provided') + '</p>' +
      '<p><strong>Corkage Fee Amount:</strong> ' + (data.corkage_fee_amount || 'Not applicable') + '</p>' +
      '<p><strong>Other Corkage Policy:</strong> ' + (data.other_corkage_policy || 'Not applicable') + '</p>' +
      '<p><strong>Wine Service Equipment:</strong> ' + (data.wine_service_equipment || 'Not provided') + '</p>' +
      '<p><strong>Equipment Other Note:</strong> ' + (data.philly_equipment_other_note || 'None') + '</p>' +
      '<p><strong>BYOB Service Level:</strong> ' + (data.byob_service_level || 'Not provided') + '</p>' +
      '<p><strong>Restaurant Type:</strong> ' + (data.philly_restaurant_type || 'Not provided') + '</p>' +
      '<p><strong>Restaurant Type Other Note:</strong> ' + (data.philly_restaurant_type_other_note || 'None') + '</p>' +
      '<p><strong>Notes:</strong> ' + (data.philly_dining_experience || 'None') + '</p>' +
      '<p><strong>Reddit Username:</strong> ' + (data.philly_reddit_username || 'Not provided') + '</p>' +
      '<p><strong>Show Reddit Username:</strong> ' + (data.show_reddit_username || 'Not provided') + '</p>' +
      '<p><strong>Contributor Email:</strong> ' + (data.philly_contact_email || 'Not provided') + '</p>' +
      '<p><strong>Processing Status:</strong> ' + (result.success ? 'Success' : 'Failed') + '</p>' +
      '<p><strong>Message:</strong> ' + (result.message || result.toString()) + '</p>' +
      '<hr>' +
      '<p><small>This email was sent by Philadelphia BYOB Recommendation System</small></p>';
    
    GmailApp.sendEmail(NOTIFICATION_EMAIL, subject, '', {
      htmlBody: body
    });
    
    Logger.log('已發送費城推薦通知郵件');
    
  } catch (error) {
    Logger.log('發送費城推薦通知郵件時發生錯誤: ' + error.toString());
  }
}

// 發送錯誤通知郵件
function sendPhillyErrorNotification(error) {
  try {
    var subject = 'Philadelphia BYOB Form Processing Error';
    var body = 
      '<h2>Philadelphia BYOB Form Processing Error</h2>' +
      '<p><strong>Error Message:</strong> ' + error.toString() + '</p>' +
      '<p><strong>Error Details:</strong> ' + (error.stack || 'No detailed information') + '</p>' +
      '<hr>' +
      '<p><small>This email was sent by Philadelphia BYOB Recommendation System</small></p>';
    
    GmailApp.sendEmail(NOTIFICATION_EMAIL, subject, '', {
      htmlBody: body
    });
    
  } catch (emailError) {
    Logger.log('發送錯誤通知郵件時發生錯誤: ' + emailError.toString());
  }
}

// 測試函數：解析最新資料
function testParsePhillyData() {
  try {
    Logger.log('=== 測試解析費城推薦資料 ===');
    var result = parsePhillyFormData();
    Logger.log('解析結果:');
    Logger.log(JSON.stringify(result, null, 2));
    return result;
  } catch (error) {
    Logger.log('測試解析費城推薦資料時發生錯誤: ' + error.toString());
  }
}

// 測試函數：完整流程測試
function testPhillyCompleteFlow() {
  try {
    Logger.log('=== 測試費城推薦完整流程 ===');
    
    var formData = parsePhillyFormData();
    if (!formData || Object.keys(formData).length === 0) {
      Logger.log('❌ 資料解析失敗');
      return;
    }
    
    Logger.log('✅ 資料解析成功');
    Logger.log('發送到 WordPress...');
    
    var result = sendToPhillyWordPress(formData);
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
function setupPhillyTrigger() {
  // 刪除現有觸發器
  var triggers = ScriptApp.getProjectTriggers();
  for (var t = 0; t < triggers.length; t++) {
    if (triggers[t].getHandlerFunction() === 'onPhillyFormSubmit') {
      ScriptApp.deleteTrigger(triggers[t]);
    }
  }
  
  // 建立新的表單提交觸發器
  ScriptApp.newTrigger('onPhillyFormSubmit')
    .forSpreadsheet(SpreadsheetApp.openById(PHILLY_SPREADSHEET_ID))
    .onFormSubmit()
    .create();
    
  Logger.log('費城推薦觸發器設定完成');
}

// 手動觸發測試（用於除錯）
function manualPhillyTest() {
  try {
    Logger.log('=== 手動測試費城推薦流程 ===');
    
    // 模擬表單提交事件
    var mockEvent = {
      source: SpreadsheetApp.openById(PHILLY_SPREADSHEET_ID),
      range: SpreadsheetApp.openById(PHILLY_SPREADSHEET_ID).getSheets()[0].getDataRange()
    };
    
    onPhillyFormSubmit(mockEvent);
    
  } catch (error) {
    Logger.log('手動測試失敗: ' + error.toString());
  }
}
