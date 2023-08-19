import pandas as pd
import numpy as np
import psycopg2

from sklearn.model_selection import train_test_split
from sklearn.naive_bayes import GaussianNB
from sklearn.metrics import accuracy_score

conn = psycopg2.connect(database="gesmerca",
                        host="192.168.1.254",
                        user="postgres",
                        password="postgres",
                        port="5432")

cursor = conn.cursor()

cursor.execute("SELECT idproduct, stock, quantity, goods_receipt_products.price as price, goods_receipt_products.created_at as created_at FROM goods_receipt_products INNER JOIN products ON products.id = goods_receipt_products.idproduct")

cols = [ x[0] for x in cursor.description]
rows = cursor.fetchall()
df = pd.DataFrame(rows, columns=cols)
#df = df.drop(['updated_at'], axis=1)

print(df)
print(df.info())

#df['created_at'] = (df['created_at'] - df['created_at'].min())  / np.timedelta64(1,'D')
df['created_at'] = (df['created_at'] - np.datetime64('1970-01-01T00:00:00Z')) / np.timedelta64(1, 's')

print(df)

# Putting feature variable to X
X = df.filter(['idproduct', 'stock', 'created_at'])

# Putting response variable to y
y = df['price']

# Splitting the data into train and test
X_train, X_test, y_train, y_test = train_test_split(X, y, train_size=0.7,test_size=0.3,random_state=50)
X_train.shape

#GaussianNB
#----------
# 0. Creación de instancia del modelo
# Con asignación de valores a hiperparámetros
model = GaussianNB(priors = None, var_smoothing = 1e-10)

#2. Entrenamiento del modelo: fit()
model = model.fit(X_train, y_train)

#3. Predicción sobre los datos de entrenamiento.
y_pred = model.predict(X_train)

#4. Métricas para calidad de resultados de clasificación con datos de prueba.
print("Accuracy:",accuracy_score(y_train, y_pred))

#Predicción individual
result = model.predict(X_test.iloc[[10]])
print(X_test.iloc[[10]], 'precio', str(result[0]))

#Export the model to file
#-------------------------
#https://blog.cambridgespark.com/deploying-a-machine-learning-model-to-the-web-725688b851c7

import pickle

with open('gesmerca_price_estimated.pkl', 'wb') as file:
  pickle.dump(model, file)

print(file.name)
