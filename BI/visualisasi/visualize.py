import pandas as pd
import matplotlib.pyplot as plt
import seaborn as sns
import json
from sklearn.tree import DecisionTreeClassifier, plot_tree
from sklearn.model_selection import train_test_split
from sklearn import metrics
from sklearn.preprocessing import LabelEncoder

# Membaca data dari file JSON
with open('data.json') as file:
    data = json.load(file)

# Konversi data ke DataFrame
df = pd.DataFrame(data)

# Mengatasi data kategorikal
label_encoders = {}
for column in df.select_dtypes(include=['object']).columns:
    label_encoders[column] = LabelEncoder()
    df[column] = label_encoders[column].fit_transform(df[column])

# Membuat scatter plot
plt.figure(figsize=(8, 6))
sns.scatterplot(x=df.columns[0], y=df.columns[1], data=df)
plt.title('Scatter Plot')
plt.xlabel(df.columns[0])
plt.ylabel(df.columns[1])
plt.tight_layout()
plt.savefig('output.png')
plt.close()  # Menutup plot setelah menyimpannya

# Membuat bar chart
plt.figure(figsize=(8, 6))
sns.barplot(x=df.columns[0], y=df.columns[1], data=df)
plt.title('Bar Chart')
plt.xlabel(df.columns[0])
plt.ylabel(df.columns[1])
plt.tight_layout()
plt.savefig('output_bar_chart.png')
plt.close()  # Menutup plot setelah menyimpannya

# Membuat line chart
plt.figure(figsize=(8, 6))
sns.lineplot(x=df.columns[0], y=df.columns[1], data=df)
plt.title('Line Chart')
plt.xlabel(df.columns[0])
plt.ylabel(df.columns[1])
plt.tight_layout()
plt.savefig('output_line_chart.png')
plt.close()  # Menutup plot setelah menyimpannya

# Menyiapkan data untuk Decision Tree
# Asumsi kolom terakhir adalah target
X = df.iloc[:, :-1]  # Fitur
y = df.iloc[:, -1]   # Target

# Memisahkan data menjadi data latih dan data uji
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.3, random_state=1)

# Membuat model Decision Tree
clf = DecisionTreeClassifier()
clf = clf.fit(X_train, y_train)

# Membuat prediksi
y_pred = clf.predict(X_test)

# Evaluasi model
print("Akurasi Model Decision Tree:", metrics.accuracy_score(y_test, y_pred))

# Visualisasi Decision Tree
plt.figure(figsize=(20,10))
plot_tree(clf, filled=True, feature_names=X.columns, class_names=[str(x) for x in clf.classes_])
plt.title('Decision Tree')
plt.savefig('output_decision_tree.png')
plt.close()  # Menutup plot setelah menyimpannya

plt.close('all')  # Menutup semua plot yang mungkin masih terbuka
