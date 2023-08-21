import pandas as pd
import numpy as np
import psycopg2
from datetime import datetime

from sklearn.model_selection import train_test_split
from sklearn.naive_bayes import GaussianNB
from sklearn.metrics import accuracy_score

conn = psycopg2.connect(database="gesmerca",
                        host="192.168.1.254",
                        user="postgres",
                        password="postgres",
                        port="5432")

cursor = conn.cursor()

cursor.execute("SELECT idproduct, stock, quantity, goods_receipt_products.price as price, CONCAT(goods_receipts.date,'T',goods_receipts.time) as datetime FROM goods_receipt_products INNER JOIN products ON products.id = goods_receipt_products.idproduct INNER JOIN goods_receipts ON goods_receipt_products.idgoodsreceipt = goods_receipts.id")

cols = [ x[0] for x in cursor.description]
rows = cursor.fetchall()
df = pd.DataFrame(rows, columns=cols)
#df = df.drop(['updated_at'], axis=1)

df.to_csv('gesmerca_estimated_price.csv', sep=',', index=False, encoding='utf-8')

print(df)
print(df.info())

#df['created_at'] = (df['created_at'] - df['created_at'].min())  / np.timedelta64(1,'D')
#df['created_at'] = (df['created_at'] - np.datetime64('1970-01-01T00:00:00Z')) / np.timedelta64(1, 's')
#df['datetime'] = pd.to_datetime(df['datetime'])

for i, dt in enumerate(df['datetime']):
  df['datetime'][i] = datetime.strptime(dt, "%Y-%m-%dT%H:%M:%S").timestamp()
print(df)

# Putting feature variable to X
X = df.filter(['idproduct', 'quantity'])

# Putting response variable to y
y = df['price'].values.astype(str)

# Splitting the data into train and test
X_train, X_test, y_train, y_test = train_test_split(X, y, train_size=0.7,test_size=0.3,random_state=50)

#GaussianNB
#----------
# 0. Creación de instancia del modelo
# Con asignación de valores a hiperparámetros
model = GaussianNB()

#2. Entrenamiento del modelo: fit()
model = model.fit(X_train, y_train)

#3. Predicción sobre los datos de entrenamiento.
y_pred = model.predict(X_train)

#4. Métricas para calidad de resultados de clasificación con datos de prueba.
print("Accuracy:",accuracy_score(y_train, y_pred))


#Predicción individual
result = model.predict(X_test.iloc[[10]])
print(X_test.iloc[[10]], 'precio', str(result[0]))

#
# Funciones auxiliares
#
import matplotlib.pyplot as plt

def graficar_predicciones(real, prediccion):
    plt.plot(real[0:len(prediccion)],color='red', label='Valor real de la acción')
    plt.plot(prediccion, color='blue', label='Predicción de la acción')
    plt.ylim(1.1 * np.min(prediccion)/2, 1.1 * np.max(prediccion))
    plt.xlabel('Tiempo')
    plt.ylabel('Valor de la acción')
    plt.legend()
    plt.show()


#
# Validación (predicción del valor de las acciones)
#
from sklearn.preprocessing import MinMaxScaler

prediccion = model.predict(X_test)

# Graficar resultados
#graficar_predicciones(y_test, prediccion)

#Export the model to file
#-------------------------
#https://blog.cambridgespark.com/deploying-a-machine-learning-model-to-the-web-725688b851c7

import pickle

with open('gesmerca_price_estimated.pkl', 'wb') as file:
  pickle.dump(model, file)

print(file.name)
